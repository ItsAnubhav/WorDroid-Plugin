<?php

add_action( 'rest_api_init','register_content_route');
add_action( 'rest_api_init','register_random_route');
add_action( 'rest_api_init','register_posts_by_category');
add_action( 'rest_api_init','register_popular_posts');
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
add_action( 'wp_head', 'wordroid_track_post_views');

function wordroid_set_post_views($postID) {
    $count_key = 'wordroid_post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
function wordroid_track_post_views ($post_id) {
    if ( !is_single() ) return;
    if ( empty ( $post_id) ) {
        global $post;
        $post_id = $post->ID;    
    }
    wordroid_set_post_views($post_id);
}

function register_random_route(){
    register_rest_route( 'wordroid/v2', '/random', array(
        'methods' => 'GET',
        'callback' => 'get_random_posts',
      ) );
}

function register_content_route(){
    register_rest_route( 'wordroid/v2', '/contents', array(
        'methods' => 'GET',
        'callback' => 'get_contents_data',
      ) );
}

function register_posts_by_category(){
	register_rest_route( 'wordroid/v2', '/category', array(
        'methods' => 'GET',
        'callback' => 'get_bulk_category_data',
      ) );
}

function register_popular_posts(){
	register_rest_route( 'wordroid/v2', '/popular', array(
        'methods' => 'GET',
        'callback' => 'get_popular_posts',
      ) );
}

function get_contents_data(){
	$result = array();
    $result['mostCommented'] = showMostCommented();
    $result['randomPosts'] = get_random_posts();
	$result['beauty'] = get_post_by_category(445,5);
    return $result;
}

function showMostCommented($count){
    $posts = array();
    $the_query = new WP_Query(array( 'posts_per_page' => $count, 'orderby' => 'comment_count', 'order'=> 'DESC' ));
    while ( $the_query->have_posts() ) : $the_query->the_post();
        $post = array();
        $post['title'] = get_the_title();
        $post['img'] = get_the_post_thumbnail_url();
		$post['id'] = get_the_ID();
        array_push($posts,$post);
    endwhile;
    return $posts;
}

function get_popular_posts($count){
	$posts = array();
	$popularpost = new WP_Query( 
		array( 
			'posts_per_page' => $count, 
			'meta_key' => 'wordroid_post_views_count', 
			'orderby' => 'meta_value_num', 
			'order' => 'DESC'  
		) 
	);
	if ( $popularpost->have_posts() ) {
    	while ( $popularpost->have_posts() ) {
			$post = array();
        	$popularpost->the_post();
			$post['title'] = get_the_title();
            $post['id'] = get_the_ID();
            $post['img'] = get_the_post_thumbnail_url();
			array_push($posts,$post);
    	}
    	wp_reset_postdata();
	} else { 
		$posts = null;
	}
	return $posts;
}

function get_random_posts(){
	$args = array(
        'post_type' => 'post',
        'orderby'   => 'rand',
        'posts_per_page' => 5, 
    );
 	$posts = array();
	$the_query = new WP_Query( $args );
 
	if ( $the_query->have_posts() ) {
    	while ( $the_query->have_posts() ) {
			$post = array();
        	$the_query->the_post();
			$post['title'] = get_the_title();
            $post['id'] = get_the_ID();
            $post['img'] = get_the_post_thumbnail_url();
			array_push($posts,$post);
    	}
    	wp_reset_postdata();
	} else { 
		$posts = null;
	}
    return $posts;
}
function get_post_by_category($category,$count){
    $posts = array();
    $args = array(
        'posts_per_page'   => $count,
        'cat'              => $category,
        'orderby'          => 'post_date',
        'post_type'        => 'post',
        'post_status'      => array('publish'),
        'author'           => 1,
    ); 
    
    $the_query = new WP_Query($args);
    while ( $the_query->have_posts() ) : $the_query->the_post();
        $post = array();
        $post['title'] = get_the_title();
        $post['img'] = get_the_post_thumbnail_url();
		$post['id'] = get_the_ID();
        array_push($posts,$post);
    endwhile;
    wp_reset_postdata();

    return $posts;
}

function get_category_data($id,$count){
	//$catId = $data['id'];
	$catId = (int)$id;
	if($catId==null)
		return "Is is null";
	//$count = (int)$data['count'];
	if($count==null)
		$count = 10;
	$result = array();
	if($catId==-999){
		$result['title'] = "Popular Posts";
		$result['category_id'] = -999;
		$result['type'] = 4;
		$result['post_count'] = (int)$count;
		$result['posts'] = get_popular_posts((int)$count);
		return $result;
	}else if($catId==-998){
		$result['title'] = "Most Commented";
		$result['category_id'] = -998;
		$result['type'] = 4;
		$result['post_count'] = (int)$count;
		$result['posts'] = showMostCommented((int)$count);
		return $result;
	}else{
		$result['title'] = get_the_category_by_ID($catId);
		$result['category_id'] = $catId;
		$result['type'] = 4;
		$result['post_count'] = (int)$count;
		$result['posts'] = get_post_by_category($catId,$count);
		return $result;
	}
}

function get_bulk_category_data($data){
	$bulk_data = array();
	$catId = $data['id'];
	$count = $data['count'];
	if($catId==null)
		return "Cat id is null";
	$catArray = explode(',', $catId);
	foreach($catArray as $c){
		$chunk = get_category_data($c,$count);
		array_push($bulk_data,$chunk);
	}

	return $bulk_data;
}
