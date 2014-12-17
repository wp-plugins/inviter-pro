<?php
global $wpdb,$bb_table_prefix,$table_prefix,$current_user;
$user_id = bp_loggedin_user_id();
if(is_bp_old_version_no_post())
{
	$pra_topic_id = bp_get_the_topic_id();
}else{
	$pra_topic_id = bbp_get_reply_id();
}
?>
<style>
#aheadzen_send_request_id h3{margin-bottom: 5px !important;margin-top: 5px !important;}
#aheadzen_send_request_id{margin-bottom:50px;display:inline-block;width:100%;}
#aheadzen_send_request_id ul{display:inline-block;width:100%;margin: 0 !important;}
#aheadzen_send_request_id ul li{text-align:center;width:16%;float:left;list-style:none !important;margin: 10px 10px 10px 0;border:1px solid #eee;padding:5px;}
#aheadzen_send_request_id .item-avatar img{margin-bottom:1px;width:50px !important;height:50px !important;}
#aheadzen_send_request_id .loginnote{padding:10px;width:80%;color:red;}
.button.send_request{margin-top:5px;border:none; background-color:#EDEDED; height:25px; cursor:pointer; vertical-align:middle; font-size:14px; color:#1FB3DD;}
p.request_sent_msg{color:orange;margin-top: 0;}
</style>
<?php aheadzen_send_invitation_usercount_msg();?>
<ul>
<?php
if ( bp_has_members( bp_ajax_querystring( 'members' ) ) )
{
	$all_member_arr = array();
	while ( bp_members() )
	{
		bp_the_member();
		$selected_user_id = bp_get_member_user_id();
		$all_member_arr[] = $selected_user_id;
		?>
		<li>
		<div class="item-avatar"><a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar(); ?></a></div>
		<div class="item">
        <div class="item-title">
           <a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a> 
           <br />
		   <?php if(aheadzen_invitation_usercan_send()){?>
			<span id="send_request_<?php echo $pra_topic_id; ?>_<?php echo $selected_user_id; ?>"><input type="button" class="button send_request" onclick="return aheazden_send_invitation('<?php echo $pra_topic_id; ?>','<?php echo $selected_user_id; ?>','one');" value="Send Request" /></span>
			<?php }else{ ?>
			<input type="button" class="button send_request" value="Send Request Over" />
			<?php }?> 
       </div>
	   </li>
		<?php
	}
}
?>
</ul>
<?php if($all_member_arr && aheadzen_invitation_usercan_send()){
$all_member_str = implode(',',$all_member_arr);
?>
<span id="send_request_<?php echo $pra_topic_id; ?>_all"><input type="button" class="button send_request" onclick="return aheazden_send_invitation('<?php echo $pra_topic_id; ?>','<?php echo $all_member_str; ?>','all');" value="Send Request to All" /></span>
<?php }?>