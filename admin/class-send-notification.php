<?php
	$appid = wp_get_option('wordroid-settings','os_app_id');

	function wp_get_option($prefix,$key = '', $default = false ) {
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

	function send_notification(){
		echo "<h2>Sending notification</h2>";
	}

	function post_notification($title,$body,$value,$img,$type){
	    $content = array(
	        "en" => $body
	        );
	    $headings = array(
	        "en" => $title
	    );
		$fields = array(
	        'app_id' => $GLOBALS['appid'],
	        'included_segments' => array('Test'),
	        'data' => array(
	        	"type" => $type,
	        	"value" => $value
	        ),
	        'big_picture' => $img,
	        'headings' => $headings,
	        'contents' => $content
	    );
		$response = sendMessage($fields);
	}

	function post_transition_action($new_status, $old_status, $post){
		 if ($old_status == 'publish' && $new_status == 'publish' && 'post' == get_post_type($post)) {
		 	$notify = wp_get_option('wordroid-settings','enable_updatepost_notify');
		 	if($notify=='on'){
			 	$type = "post";
				$title =  wp_get_option('wordroid-settings','update_notify_title');
				$post_title = get_the_title($post);
				$post_id 	= get_the_ID($post);
				$thumbnail = get_the_post_thumbnail_url($post,'full');
				$response =  post_notification($title,$post_title,$post_id,$thumbnail,'post');
				if($response === NULL){
					return;
				}else{
					echo $response;
				}
			}
		}else if ($old_status != 'publish' && $new_status == 'publish' && 'post' == get_post_type($post)) {
			$notify = wp_get_option('wordroid-settings','enable_newpost_notify');
			if($notify=='on'){
				$type = "post";
				$title =  wp_get_option('wordroid-settings','new_notify_title');
				$post_title = get_the_title($post);
				$post_id 	= get_the_ID($post);
				$thumbnail = get_the_post_thumbnail_url($post,'full');
				$response =  post_notification($title,$post_title,$post_id,$thumbnail,'post');
				if($response === NULL) return;
			}
		}
	}

	function sendMessage($fields_array){
		$apikey = wp_get_option('wordroid-settings','os_api_key');
		/*$appid = "82fa91c3-9c88-4c3f-9fc5-dc88a0149038";
		$apikey = "NGM3NTM1NTktYWQ3OS00OWUyLTkxMjktMjk1NGY2ZWU4N2Jk";
	    $content = array(
	        "en" => $content
	        );
	    $headings = array(
	        "en" => $title
	    );

	    $fields = array(
	        'app_id' => $appid,
	        'included_segments' => array('All'),
	        'data' => array(
	        	"post_id" => $postid
	        ),
	        'big_picture' => $image,
	        'headings' => $headings,
	        'contents' => $content
	    );*/

	    $fields = json_encode($fields_array);
		//print("\nJSON sent:\n");
		//print($fields);

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
	                                               'Authorization: Basic '.$apikey));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_HEADER, FALSE);
	    curl_setopt($ch, CURLOPT_POST, TRUE);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    

	    $response = curl_exec($ch);
	    curl_close($ch);

		$result_data = json_decode($response, true);
		return $result_data;
	}

?>
