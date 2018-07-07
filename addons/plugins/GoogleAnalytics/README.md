# Google Analytics Plugin

Adds a Google Analytics tracking script to every page.

## Installation

[Download](https://github.com/esotalk/GoogleAnalytics/archive/master.zip) or clone the Google Analytics plugin repo into your esoTalk plugin directory:

	cd ESOTALK_DIR/addons/plugins/
	git clone https://github.com/esotalk/GoogleAnalytics.git GoogleAnalytics

Navigate to the the admin/plugins page and activate the Google Analytics plugin.

## Translation

Create `definitions.GoogleAnalytics.php` in your language pack with the following definitions:

	$definitions["Tracking ID"] = "Tracking ID";
	$definitions["message.trackingIdHelp"] = "Get your Tracking ID by going into the <em>Administration</em> section for your Google Analytics Property and selecting <em>Property Settings</em>.";