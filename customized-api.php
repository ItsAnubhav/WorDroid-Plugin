<?php
add_filter( 'rest_allow_anonymous_comments', '__return_true' );
add_action( 'plugins_loaded', 'better_rest_api_featured_images_load_translations' );
/**
 * Load translation files.
 *
 * @since  1.2.0
 */
function better_rest_api_featured_images_load_translations() {
    load_plugin_textdomain( 'better-rest-api-featured-images', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}

add_action( 'init', 'better_rest_api_featured_images_init', 12 );
/**
 * Register our enhanced better_featured_image field to all public post types
 * that support post thumbnails.
 *
 * @since  1.0.0
 */
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

/**
 * Return the better_featured_image field.
 *
 * @since   1.0.0
 *
 * @param   object  $object      The response object.
 * @param   string  $field_name  The name of the field to add.
 * @param   object  $request     The WP_REST_Request object.
 *
 * @return  object|null
 */
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
	//$featured_image['media_type']    = wp_attachment_is_image( $image_id ) ? 'image' : 'file';
	//$featured_image['media_details'] = wp_get_attachment_metadata( $image_id );
	//$featured_image['post']          = ! empty( $image->post_parent ) ? (int) $image->post_parent : null;
	//$featured_image['post']          = $catName->name;
	$featured_image['source_url']    = wp_get_attachment_url( $image_id );
	$featured_image['post_thumbnail']= get_the_post_thumbnail_url($image->post_parent,'post-thumbnail'); 

	/*if ( empty( $featured_image['media_details'] ) ) {
		$featured_image['media_details'] = new stdClass;
	} elseif ( ! empty( $featured_image['media_details']['sizes'] ) ) {
		$img_url_basename = wp_basename( $featured_image['source_url'] );
		foreach ( $featured_image['media_details']['sizes'] as $size => &$size_data ) {
			$image_src = wp_get_attachment_image_src( $image_id, $size );
			if ( ! $image_src ) {
				continue;
			}
			$size_data['source_url'] = $image_src[0];
		}
	} elseif ( is_string( $featured_image['media_details'] ) ) {
		// This was added to work around conflicts with plugins that cause
		// wp_get_attachment_metadata() to return a string.
		$featured_image['media_details'] = new stdClass();
		$featured_image['media_details']->sizes = new stdClass();
	} else {
		$featured_image['media_details']['sizes'] = new stdClass;
	}*/

	return apply_filters( 'better_rest_api_featured_image', $featured_image, $image_id );
}