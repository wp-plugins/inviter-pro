<?php
/*
Plugin Name: Inviter Pro
Plugin URI: http://aheadzen.com/
Description: The plugin add options for user invitation on topic detail page of buddypress(bbpress). It send invitaion via acitviy, notification & email.
Author: Aheadzen Team
Version: 1.0.0
Author URI: http://aheadzen.com/

Copyright: © 2014-2015 ASK-ORACLE.COM
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
$noposts_plugin_dir_path = dirname(__FILE__);
$noposts_plugin_dir_url = plugins_url('', __FILE__);

$max_number_post_request = 100;
/****************************
Send Topic JS code
****************************/
function aheadzen_send_request_js() {
	global $noposts_plugin_dir_url;
	aheadzen_is_bp_topic_send_invitation();
	if(aheadzen_is_bp_topic_send_invitation()){
	?>
	<script type="text/javascript">
	var site_url = '<?php echo site_url('/'); ?>';
	var plugin_root_url = '<?php echo $noposts_plugin_dir_url; ?>';
	var current_user_id = '<?php echo bp_loggedin_user_id();?>';
	var topic_link = '<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>';
	<?php if(is_bp_old_version_no_post()){?>
	var topic_title = '<?php bp_the_topic_title(); ?>';
	<?php }else{?>
	var topic_title = '<?php bbp_topic_title(); ?>';
	<?php }?>	
	function aheazden_send_invitation(topic_id, to_userid,toact)
	{
		if(toact=='all'){
			var buttonid = 'send_request_'+topic_id+'_all';
		}else{
			var buttonid = 'send_request_'+topic_id+'_'+to_userid;
		}
		if(topic_id == ""){
			alert("Please Select the Topic");return false;
		}else{
			jQuery('#'+buttonid).html('sending ...');
			var ajaxurl = site_url+"/";
			jQuery.post(ajaxurl, { act: "aheadzen_send_invitation", to_userid: to_userid, topic_id: topic_id, topic_link: topic_link, topic_title: topic_title })
				.done(function( data ) {
					jQuery('#'+buttonid).html('<input type="button" class="button request_sent" value="Request Sent" />');
					//alert( "Data Loaded: " + data );
			});
		}
	}
	</script>
	<?php 
	}
}

/****************************
add user list for invite users
****************************/
//add_action('bp_before_group_forum_topic_posts','aheadzen_bp_after_group_forum_topic_posts');
add_action('bbp_theme_after_reply_content','aheadzen_bp_after_group_forum_topic_posts_new');
add_action('bp_after_group_forum_topic_posts','aheadzen_bp_after_group_forum_topic_posts');
function aheadzen_bp_after_group_forum_topic_posts_new()
{
	if(!is_bp_old_version_no_post())
	{
		$reply_id = bbp_get_reply_id();
		$reply_content = bbp_get_reply($reply_id);
		if(!$reply_content){aheadzen_bp_after_group_forum_topic_posts();}
	}
}
function aheadzen_bp_after_group_forum_topic_posts()
{
	aheadzen_send_request_js();
?>	<h5 style="clear:both;display:inline-block;margin-bottom: 25px;" id="aheadzen_send_invitation_click"><< Click to Send Invitation Request >></h5>

	<script>
	jQuery( "#aheadzen_send_invitation_click" ).click(function() {
		jQuery( "#aheadzen_send_request_id" ).toggle( "slow", function() {
		// Animation complete.
		});
		
	});
	</script>
	<div style="display:none;" id="aheadzen_send_request_id">
	<?php
	global $current_user;
	if($current_user->ID){
		include_once('pra_user_request.php');
	}else{
		echo '<style>#aheadzen_send_request_id{margin-bottom:50px;display:inline-block;width:100%;}</style><div class="loginnote">Please login to send request</div>';
	}
	?>
	</div>
<?php
}

/****************************
check is older buddypress version
****************************/
function is_bp_old_version_no_post() {
 
	if(function_exists('bbp_get_reply_id' ) && function_exists('bbp_get_topic'))
	return false;

	if(function_exists('bp_get_the_topic_id')) return true;
	
}

/****************************
send invitation activity, notification & email.
****************************/
add_action('bp_init','aheadzen_send_invitation_frnd', 999);
function aheadzen_send_invitation_frnd()
{
	global $current_user,$bp,$max_number_post_request;
	if($_POST && $_POST['act']=='aheadzen_send_invitation' && $_POST['topic_id'] && $_POST['to_userid'] && $current_user->ID)
	{
		$current_user_id = $current_user->ID;
		$to_userid = $_POST['to_userid'];
		$topic_id = $_POST['topic_id'];
		$topic_link = $_POST['topic_link'];
		$topic_title = $_POST['topic_title'];
		$current_user_link = bp_core_get_userlink( $current_user_id );
		$sent_dt = date('Y-m-d h:i:s');
		$to_userid_arr = explode(',',$to_userid);
		if($to_userid_arr){
			$user_sent_request_arr = array();
			for($t=0;$t<count($to_userid_arr);$t++){
				if(!aheadzen_invitation_usercan_send()){return false;}
				$user_sent_request_arr = array('tid'=>$topic_id,'uid'=>$to_userid,'dt'=>$sent_dt);
				$to_userid = $to_userid_arr[$t];
				$to_userid_link = bp_core_get_userlink( $to_userid );
				/*Activity add start*/
				$post_title = $topic_title;
				$mytopic_link = '<a href="' . $topic_link .'">' . $post_title . '</a>';
				echo $action_content = sprintf( __( "%s invited %s for topic %s", 'buddypress' ), $current_user_link, $to_userid_link, $mytopic_link );
				$arg_arr = array(
							'user_id'   => $to_userid,
							'action'    => $action_content,
							'component' => 'inviter',
							'item_id'    => $topic_id, 
							'secondary_item_id' => '0',
							'type'      => 'forums'
						);				
				$activity_id = bp_activity_add($arg_arr);
				/*Activity add end*/
				
				if($to_userid)
				{
					/*Notification add start*/
					bp_core_add_notification($topic_id, $to_userid, 'inviter', $current_user_id.'_sentnotification');
					/*Notification add end*/
					
					/*Email sent add start*/
					$from_name =  get_option('blogname');
					$from_email = get_option('admin_email');					
					$to = $to_userid;
					$to_display_name = bp_core_get_user_displayname($to);
					$user_data = get_userdata($to);
					$to_email = $user_data->data->user_email;					
					$user_display_name = bp_core_get_user_displayname($current_user_id);
					$component_action_type = $type;
					$sender_link = bp_core_get_userlink( $user_id );
					$subject = '';					
					$notification = $action_content;
					
					$subject = "$user_display_name sent you invitation for topic $post_title";
					$notification_link = $bp->bp_nav['notifications']['link'];
					$settings_link = '<a href="'.$bp->bp_nav['settings']['link'].'notifications/"> member settings</a>';
					$message =  $notification.'<br /><br />To view all of your pending notifications: <a href="'.$notification_link.'">Click the link</a> <br /><br />Click to view '.$sender_link.'\'s profile.';
					$message .= '<br /><br />'.sprintf( __( 'To disable these notifications please log in and go to: %s', 'aheadzen' ), $settings_link );
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type: text/html; charset=".get_bloginfo('charset')."" . "\r\n";
					$headers .= "From: $from_name <$from_email>" . "\r\n";
					//echo "to : $to_email<br />, SUBJECT: $subject<br />, Message: $message<br />,Header : $headers";exit;
					wp_mail($to_email, $subject, $message, $headers);
					/*Email sent add end*/
				}
				
				$inviter_pro_user_sent_request = get_user_meta($current_user_id,'inviter_pro_user_sent_request',true);
				$inviter_pro_user_sent_request[] = $user_sent_request_arr;
				$inviter_pro_user_sent_request1 = $inviter_pro_user_sent_request;
				update_user_meta($current_user_id,'inviter_pro_user_sent_request',$inviter_pro_user_sent_request1);
			}			
		}
		
	exit;
	}	
}

/*************************************************
Check is the Buddypress Topic
*************************************************/
function aheadzen_is_bp_topic_send_invitation()
{
	global $bp;
	if($bp)
	{
		$check_url_for_topic = $bp->unfiltered_uri;
		$current_component = $bp->current_component;
		$current_action = $bp->current_action;
		if($current_component=='groups' && $current_action=='forum')
		{
			return 1;
		}
	}
	return 0;
}



add_filter( 'bp_notifications_get_registered_components', 'aheadzen_send_invitation_filter_notifications_get_registered_components', 10 );
add_filter('bp_notifications_get_notifications_for_user','aheadzen_send_invitation_notification_title_format','',3);
add_action( 'bp_setup_globals', 'aheadzen_send_invitation_setup_globals',9 );

/*************************************************
Register Buddpress voter component
*************************************************/
function aheadzen_send_invitation_filter_notifications_get_registered_components( $component_names = array() ) {
 // Force $component_names to be an array
 if ( ! is_array( $component_names ) ) {
  $component_names = array();
 }
 // Add 'inviter' component to registered components array
 array_push( $component_names, 'inviter' );
 // Return component's with 'inviter' appended
 return $component_names;
}


/*************************************************
Set voter buddypress componant Global
*************************************************/
function aheadzen_send_invitation_setup_globals()
{
	global $bp;
	$bp->inviter = new BP_Component;
	$bp->inviter->notification_callback = 'aheadzen_send_invitation_notification_title_format';
	$bp->active_components['inviter'] = '1';
}

/*************************************************
User Notification
*************************************************/
function aheadzen_send_invitation_notification_title_format( $component_action, $item_id, $secondary_item_id ) {
   
  global $bp,$wp_query;
   $component_action_arr = explode('_',$component_action);
   $sender_use_id = $component_action_arr[0];
   $notification_type = $component_action_arr[1];
   
	$sender_display_name = bp_core_get_user_displayname( $sender_use_id );
	$sender_url = bp_core_get_user_domain( $sender_use_id );

	if(is_bp_old_version_no_post())
	{			
		$topic_details = bp_forums_get_topic_details( $item_id );
	}else{
		$topic_details = bbp_get_topic( $item_id );
	}
	if($topic_details){
		$post_title = $topic_details->post_title;
		$post_author = $topic_details->post_author;
		if(is_bp_old_version_no_post())
		{ 
			$post_title = $topic_details->topic_title;
			$group = groups_get_group( array( 'group_id' => $topic_details->object_id ) );
			$topic_link = trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug . '/' ).'/forum/topic/' . $topic_details->topic_slug . '/';
			$topic_post= bp_forums_get_post( $post_id );
			$post_author = $topic_post->poster_id;
		}else{
			$topic_link = bbp_get_topic_permalink( $post_id );
			$post_author = bbp_get_reply_author( $post_id );
		}
		$topic_link = '<a href="' . $topic_link .'">' . $post_title . '</a>';
		$notification = "$sender_display_name invited you for $topic_link";
	}
	
	return $notification;
}

function aheadzen_send_invitation_usercount()
{
	global $current_user;
	$current_user_id = $current_user->ID;
	$inviter_pro_user_sent_request_count = 0;
	$inviter_pro_user_sent_request = get_user_meta($current_user_id,'inviter_pro_user_sent_request',true);
	if($inviter_pro_user_sent_request){
		$inviter_pro_user_sent_request_count = count($inviter_pro_user_sent_request);
	}
	return $inviter_pro_user_sent_request_count;
}

function aheadzen_invitation_usercan_send()
{
	global $current_user,$max_number_post_request;
	$is_user_send_request = 1;
	$inviter_pro_user_sent_request_count = aheadzen_send_invitation_usercount();	
	if($inviter_pro_user_sent_request_count >= $max_number_post_request){return false;}
	return true;
}

function aheadzen_send_invitation_usercount_msg()
{
	global $current_user,$max_number_post_request;
	$inviter_pro_user_sent_request_count = aheadzen_send_invitation_usercount();
	$remaining=$max_number_post_request-$inviter_pro_user_sent_request_count;
	echo "<p class=\"request_sent_msg\">You have sent $inviter_pro_user_sent_request_count invitation requests out of $max_number_post_request.  <b>Remaining invitation requests are $remaining.</b></p>";

}