# Member Notifications Plugin

Allows users to follow members and get notified about new posts by them.

## Installation

[Download](https://github.com/esotalk/MemberNotifications/archive/master.zip) or clone the Member Notifications plugin repo into your esoTalk plugin directory:

	cd ESOTALK_DIR/addons/plugins/
	git clone git@github.com:esotalk/MemberNotifications.git MemberNotifications

Navigate to the the admin/plugins page and activate the MemberNotifications plugin.

## Translation

Create `definitions.MemberNotifications.php` in your language pack with the following definitions:

	$definitions["email.postMember.body"] = "<p><strong>%1\$s</strong> has posted in a conversation: <strong>%2\$s</strong></p><hr>%3\$s<hr><p>To view the new activity, check out the following link:<br>%4\$s</p>";
	$definitions["email.postMember.subject"] = "There is a new post by %1\$s";
	$definitions["Email me when there is a new post by a member I have followed"] = "Email me when there is a new post by a member I have followed";