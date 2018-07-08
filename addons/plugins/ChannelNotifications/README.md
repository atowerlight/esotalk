# Channel Notifications Plugin

Allows users to follow channels and get notified about new posts within them.

## Installation

[Download](https://github.com/esotalk/ChannelNotifications/archive/master.zip) or clone the Channel Notifications plugin repo into your esoTalk plugin directory:

	cd ESOTALK_DIR/addons/plugins/
	git clone git@github.com:esotalk/ChannelNotifications.git ChannelNotifications

Navigate to the the admin/plugins page and activate the ChannelNotifications plugin.

## Translation

Create `definitions.ChannelNotifications.php` in your language pack with the following definitions:

	$definitions["email.postChannel.body"] = "<p><strong>%1\$s</strong> has posted in a conversation in a channel which you followed: <strong>%2\$s</strong></p><hr>%3\$s<hr><p>To view the new activity, check out the following link:<br>%4\$s</p>";
	$definitions["email.postChannel.subject"] = "[%1\$s] %2\$s";
	$definitions["Email me when someone posts in a channel I have followed"] = "Email me when someone posts in a channel I have followed";