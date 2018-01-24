<?php

	function send_notification(){
		echo "<h2>Sending notification</h2>";
	}

	function post_transition_action($new_status, $old_status, $post){
		 if ($old_status == 'publish' && $new_status == 'publish' && 'post' == get_post_type($post)) {
		 	$type = "post";
			$title = "Updated Post";
			$post_title = get_the_title($post);
			$post_id 	= get_the_ID($post);
			$thumbnail = get_the_post_thumbnail_url($post,'full');
			$response =  sendMessage($title,$post_title,$post_id,$thumbnail,$type);
			if($response === NULL){
				return;
			}else{
				echo $response;
			}
		}else if ($old_status != 'publish' && $new_status == 'publish' && 'post' == get_post_type($post)) {
			$type = "post";
			$title = "New Post";
			$post_title = get_the_title($post);
			$post_id 	= get_the_ID($post);
			$thumbnail = get_the_post_thumbnail_url($post,'full');
			$response =  sendMessage($title,$post_title,$post_id,$thumbnail,$type);
			if($response === NULL) return;
		}
	}


	function sendMessage($title,$content,$postid,$image,$type){
		$appid = "82fa91c3-9c88-4c3f-9fc5-dc88a0149038";
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
	    );

	    $fields = json_encode($fields);
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