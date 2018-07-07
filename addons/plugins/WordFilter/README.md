# Word Filter Plugin

Perform find and replace on post content when posts are displayed.

## Installation

[Download](https://github.com/esotalk/WordFilter/archive/master.zip) or clone the Word Filter plugin repo into your esoTalk plugin directory:

	cd ESOTALK_DIR/addons/plugins/
	git clone git@github.com:esotalk/WordFilter.git WordFilter

Navigate to the the admin/plugins page and activate the Word Filter plugin.

## Translation

Create `definitions.WordFilter.php` in your language pack with the following definitions:

	$definitions["Word filters"] = "Word filters";
	$definitions["message.wordFilterInstructions"] = "Enter each word on a new line. Optionally specify a replacement after a vertical bar (|) character; otherwise, the word will be replaced with asterisks (*). Words are case-insensitive. Regular expressions are allowed.";
