<?php
add_filter( 'rest_allow_anonymous_comments', '__return_true' );
add_action( 'plugins_loaded', 'better_rest_api_featured_images_load_translations' );
add_action( 'init', 'better_rest_api_featured_images_init', 12 );
add_action( 'rest_api_init','register_settings_route');
/**
 * Load translation files.
 *
 * @since  1.2.0
 */
function register_settings_route(){
	register_rest_route( 'wordroid/v2', '/settings', array(
    'methods' => 'GET',
    'callback' => 'get_settings_data',
  ) );
}
function get_settings_data(){
	//Update Title and Body
	$update_title = substr(wordroid_get_option('wordroid-update','update_title'),0,50);
	if($update_title==false)
		$update_title = null;
	$update_body = wordroid_get_option('wordroid-update','update_body');
	if($update_body==false)
		$update_body = null;
	//User key
	$user_key = wordroid_get_option('wordroid-settings','app_user_key');
	if(!$user_key)
		$user_key = null;
	//App Version
	$version = wordroid_get_option('wordroid-update','version');
	$version = (int)$version;
	//Categories on home screen
	$category = wordroid_get_option('wordroid-config','home_screen_categories');
	if($category==false)
		$category = array();
	//Force Update
	$force_update = wordroid_get_option('wordroid-update','force_update');
	if($force_update == 'on')
		$force_update = true;
	//Slider Settings
	$slider_enabled = wordroid_get_option('wordroid-config','slider_enabled');
	if($slider_enabled == 'on')
		$slider_enabled = true;
	//Sections settings
	$sections = wordroid_get_option('wordroid-config','wordroid_section_group');
	if($sections == false)
		$sections = array();
	$settings_data = array(
	    'app_title' => wordroid_get_option('wordroid-config','app_name'),
	    'user_key' => $user_key,
	    'update_title' => $update_title,
	    'update_body' => $update_body,
	    'app_version' => $version,
	    'force_update' => $force_update,
		'slider_enabled'   => $slider_enabled,
		'slider_cat'   => wordroid_get_option('wordroid-config','slider_category'),
	    'sections' => $sections,
		'categories' => $category,
	);
	return $settings_data;
}
function wordroid_get_option($prefix,$key = '', $default = false ) {
		if ( function_exists( 'cmb2_get_option' ) ) {
			// Use cmb2_get_option as it passes through some key filters.
			return cmb2_get_option( $prefix, $key, $default );
		}
		// Fallback to get_option if CMB2 is not loaded yet.
		$opts = get_option( $prefix, $default );
		$val = $default;
		if ( 'all' == $key ) {
			$val = $opts;
		} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
			$val = $opts[ $key ];
		}
		return $val;
}
function better_rest_api_featured_images_load_translations() {
    load_plugin_textdomain( 'better-rest-api-featured-images', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
function wordroid_rest_api_comments($object, $field_name, $request){
	if(!empty($object['id'])){
		$comment_id = $object['id'];
	}else{
		return null;
	}
	$args = array(
		'parent' => $comment_id, // use user_id
		'count' => true //return only the count
	);
	$comments = get_comments($args);
	return $comments;
}
function better_rest_api_featured_images_init() {
	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	register_rest_field( 'comment',
					'child_comment_count',
					array(
						'get_callback' => 'wordroid_rest_api_comments',
						'schema'       => null,
					)
				);
	foreach ( $post_types as $post_type ) {

		$post_type_name     = $post_type->name;
		$show_in_rest       = ( isset( $post_type->show_in_rest ) && $post_type->show_in_rest ) ? true : false;
		$supports_thumbnail = post_type_supports( $post_type_name, 'thumbnail' );
		$supports_author	= post_type_supports( $post_type_name, 'author' );
		
		// Only proceed if the post type is set to be accessible over the REST API
		// and supports featured images.
		if ( $show_in_rest && $supports_thumbnail ) {

			// Compatibility with the REST API v2 beta 9+
			if ( function_exists( 'register_rest_field' ) ) {
				register_rest_field( $post_type_name,
					'better_featured_image',
					array(
						'get_callback' => 'better_rest_api_featured_images_get_field',
						'schema'       => null,
					)
				);
			} elseif ( function_exists( 'register_api_field' ) ) {
				register_api_field( $post_type_name,
					'better_featured_image',
					array(
						'get_callback' => 'better_rest_api_featured_images_get_field',
						'schema'       => null,
					)
				);
			}
		}
		register_rest_field( $post_type_name,
					'categories_detail',
					array(
						'get_callback' => 'wordroid_rest_api_categories',
						'schema'       => null,
					)
				);
		if ( $show_in_rest && $supports_author ) {
			if ( function_exists( 'register_rest_field' ) ) {
				register_rest_field( $post_type_name,
							'author_name',
							array(
								'get_callback' => 'wordroid_rest_api_author',
								'schema'       => null,
							)
				);
			}elseif ( function_exists( 'register_api_field' ) ) {
				register_api_field( $post_type_name,
							'author_name',
							array(
								'get_callback' => 'wordroid_rest_api_author',
								'schema'       => null,
							)
				);
			}
		}
	}
}

// Add filter to respond with next and previous post in post response.
add_filter( 'rest_prepare_post', function( $response, $post, $request ) {
  // Only do this for single post requests.
        global $post;
        // Get the so-called next post.
        $next = get_adjacent_post( false, '', false );
        // Get the so-called previous post.
        $previous = get_adjacent_post( false, '', true );
        // Format them a bit and only send id and slug (or null, if there is no next/previous post).
        $response->data['next'] = ( is_a( $next, 'WP_Post') ) ? array( "id" => $next->ID, "slug" => $next->post_name ) : null;
        $response->data['previous'] = ( is_a( $previous, 'WP_Post') ) ? array( "id" => $previous->ID, "slug" => $previous->post_name ) : null;
    return $response;
}, 10, 3 );



function wordroid_prev_post($object, $field_name, $request){

}

function wordroid_rest_api_author($object, $field_name, $request){
	if(!empty($object['author'])){
		$author_id = $object['author'];
	}else{
		return null;
	}
	$author_name = get_the_author_meta( 'display_name' , $author_id );
	return apply_filters( 'wordroid_author', $author_name, $author_id );
}

function wordroid_rest_api_categories($object, $field_name, $request){
	if(!empty($object['categories'])){
		$categories = $object['categories'];
	}else{
		return null;
	}
	if(sizeof($categories)==0){
		return null;
	}
	$category_obj = [];
	
	foreach($categories as $cat) {
		$category = get_category($cat);
		$array = [];
		$array['id'] = $cat;
		$array['name'] = $category->name;
		$array['description'] = $category->description;
		$array['slug'] = $category->slug;
		$array['count'] = $category->count;
		$array['parent'] = $category->parent;
		array_push($category_obj,$array);
	}
	return apply_filters( 'wordroid_categories', $category_obj, $image_id );
}
function better_rest_api_featured_images_get_field( $object, $field_name, $request ) {

	// Only proceed if the post has a featured image.
	if ( ! empty( $object['featured_media'] ) ) {
		$image_id = (int)$object['featured_media'];
	} elseif ( ! empty( $object['featured_image'] ) ) {
		// This was added for backwards compatibility with < WP REST API v2 Beta 11.
		$image_id = (int)$object['featured_image'];
	} else {
		return null;
	}

	$image = get_post( $image_id );

	if ( ! $image ) {
		return null;
	}
	// This is taken from WP_REST_Attachments_Controller::prepare_item_for_response().
	$featured_image['id']            = $image_id;
	$featured_image['alt_text']      = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	$featured_image['caption']       = $image->post_excerpt;
	$featured_image['description']   = $image->post_content;
	$featured_image['source_url']    = wp_get_attachment_url( $image_id );
	$featured_image['medium_large']  = get_the_post_thumbnail_url($image->post_parent,'medium_large'); 
	$featured_image['post_thumbnail']= get_the_post_thumbnail_url($image->post_parent,'post-thumbnail'); 
	return apply_filters( 'better_rest_api_featured_image', $featured_image, $image_id );
}
