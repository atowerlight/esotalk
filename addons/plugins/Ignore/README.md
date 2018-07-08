# Ignore Plugin

Allows users to ignore other users and hide their posts.

## Installation

[Download](https://github.com/esotalk/Ignore/archive/master.zip) or clone the Ignore plugin repo into your esoTalk plugin directory:

	cd ESOTALK_DIR/addons/plugins/
	git clone git@github.com:esotalk/Ignore.git Ignore

Navigate to the the admin/plugins page and activate the Ignore plugin.

## Translation

Create `definitions.Ignore.php` in your language pack with the following definitions:

    $definitions["Unignore"] = "Unignore";    
	$definitions["Unignore member"] = "Unignore member";	
    $definitions["Ignore member"] = "Ignore member";    
	$definitions["Ignored"] = "Ignored";	
	$definitions["Ignored"] = "Ignored";	
	$definitions["message.noIgnoredMembers"] = "You haven't ignored any members. To ignore a member, go to their profile and choose <strong>Controls &rarr; Ignore member</strong>.";	
