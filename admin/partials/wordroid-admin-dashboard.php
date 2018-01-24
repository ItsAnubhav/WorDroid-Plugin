<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://itsanubhav.com
 * @since      1.0.0
 *
 * @package    Wordroid
 * @subpackage Wordroid/admin/partials
 */
	
	if(isset($_POST['send_notification'])){ 
		if(isset($_POST['notification-list'])){
			$selection  = $_POST['notification-list'];
		}else {
			$selection = 'post';
		}
		//Post Notifications
		if($selection=='post'){
			$post_title		= $_POST['post_title'];
			$post_id 	= $_POST['post_id'];
			$post_img	= $_POST['post_img'];
			send_post_notification($post_title);
		}else if($selection=='web'){
			//Web Notification
			$web_title 	= $_POST['web_title'];
			$web_url	= $_POST['web_url'];
			$web_img    = $_POST['web_img'];
		}else if($selection=='message'){
			//fcm_notif_submit($title, $content, $target, $s_regid);
			//Message Notification
			$msg_title = $_POST['msg_title'];
			$msg_content = $_POST['msg_content'];
		}
	}


?>
<style>
	table tr{border-bottom: 1px solid #333!important;}
</style>
<h2>Send Notifications</h2>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

	<div class="postbox">
		<div class="inside">

	<form name="notif_form" action="" id="notif_form" method="post"> 
			 <table class="notification-list-table">
			  <tr>
			    <th style="width:250px;" align="left"><?php _e('Notification Type')?></th>
			    <th><select onchange="set_notify_type()" style="width:200px" name="notification-list" id="notification-list">
					  <option <?php if ($selection=='post') { echo ' selected="selected"'; } ?> value="post">Post Notification</option>
					  <option <?php if ($selection=='web') { echo ' selected="selected"'; } ?> value="web">WebView Notification</option>
					  <option <?php if ($selection=='message') { echo ' selected="selected"'; } ?> value="message">Message</option>
					</select></th>
			  </tr>
			</table> 
			<hr>
			<h3 id="notification-title">Post Notification</h3>
			<table id="post-notification">
				<tr>
					<td style="width:250px;" align="left"><b>Post Title</b></td>
					<td><input style="width:300px;" type="text" name="post_title" value="<?php echo $post_title; ?>"></td>
				</tr>
				<tr>
					<td style="width:250px;" align="left"><b>Post ID</b></td>
					<td><input style="width:100px;" type="text" name="post_id" value="<?php echo $post_id; ?>"></td>
				</tr>
				<tr>
					<td style="width:250px;" align="left"><b>Big Image</b></td>
					<td><input style="width:300px;" type="text" name="post_img" value="<?php echo $post_img; ?>"></td>
				</tr>
			</table>
			<table id="web-notification">
				<tr>
					<td style="width:250px;" align="left"><b>Notification Title</b></td>
					<td><input style="width:300px;" type="text" name="web_title" value="<?php echo $web_title; ?>"></td>
				</tr>
				<tr>
					<td style="width:250px;" align="left"><b>Web URL</b></td>
					<td><input style="width:300px;" type="text" name="web_url" value="<?php echo $web_url; ?>"></td>
				</tr>
				<tr>
					<td style="width:250px;" align="left"><b>Big Image</b></td>
					<td><input style="width:300px;" type="text" name="web_img" value="<?php echo $web_img; ?>"></td>
				</tr>
			</table>
			<table id="msg-notification">
				<tr>
					<td style="width:250px;" align="left"><b>Notification Title</b></td>
					<td><input style="width:300px;" type="text" name="msg_title" value="<?php echo $msg_title; ?>"></td>
				</tr>
				<tr>
					<td style="width:250px;" align="left"><b>Message Body (Supports HTML Tags)</b></td>
					<td><textarea style="width:300px;" id="msg_content" name="msg_content" type="text" rows="4"><?php echo $msg_content; ?></textarea><br></td>
				</tr>
			</table>
			<br/>
		<input type="submit" value="Send Now" name="send_notification" id="send_notification" class="button button-primary">
	</form>
		</div>
	</div>
	
</div>
<script type="text/javascript">
	function set_notify_type(){
		var x = document.getElementById("notification-list").value;
		if(x=='post'){
			document.getElementById("post-notification").style.display = 'block';
			document.getElementById("web-notification").style.display = 'none';
			document.getElementById("msg-notification").style.display = 'none';
			document.getElementById("notification-title").innerText = "Post Notification";
		}else if(x=='web'){
			document.getElementById("post-notification").style.display = 'none';
			document.getElementById("web-notification").style.display = 'block';
			document.getElementById("msg-notification").style.display = 'none';
			document.getElementById("notification-title").innerText = "Web Notification";
		}else if(x=='message'){
			document.getElementById("post-notification").style.display = 'none';
			document.getElementById("web-notification").style.display = 'none';
			document.getElementById("msg-notification").style.display = 'block';
			document.getElementById("notification-title").innerText = "Message Notification";
		}
	}
	set_notify_type();
</script>