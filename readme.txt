=== Inviter Pro Plugin ===
Contributors: aheadzen
Tags: inviter, activity, notification, email ,buddypress, forum topics
Requires at least : 3.0.0
Tested up to: 4.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The plugin add options for user invitation on forum(bbpress) topic detail page. It send invitaion to buddypress members via acitviy, notification & email.

== Description ==

If you want to send the topic page visit invitation to buddypress members via buddypress acitviy, notification & email. The plugin is good option for use.
The plugin will add send invitation option with all buddypress member list to forum topic detail page for users to send invitations. 
You can send individual member invitation and same way you can send invitation to all members at a same time while you using the plugin.
The user must login to send user invitation and the defaul maximum limit to send invitation is 100 only. So after 100 user invitation user cannot send any more invitations.
All user sent invitation data are stored in user meta for each logged user.

<h4>Features :</h4>
<ul>
<li>• Buddypress member invitation to forum topic. </li>
<li>• Invitation via buddypress activity, notification.</li>
<li>• Send invitation via email.</li>
<li>• maximum user send request is editable from plugin code. default is : 100</li>
</ul>

== Installation ==
1. Unzip and upload plugin folder to your /wp-content/plugins/ directory  OR Go to wp-admin > plugins > Add new Plugin & Upload plugin zip.
2. Go to wp-admin > Plugins(left menu) > Activate the plugin

== Screenshots ==
1. Plugin Activation
2. Topic Detail page

== Configuration ==

1. User sent request data is stored in user meta with variable name = "inviter_pro_user_sent_request"
2. User sent request data is stored in with data of topic id, sent member user ID and the sent data.
3. The maximum request sent limit is : max_number_post_request = 100; and you can change from inviter_pro.php file at starting code.

== Changelog ==

= 1.0.0.0 =
* Fresh Public Release.
