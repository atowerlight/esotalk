<?php
// Copyright 2014 Toby Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["GoogleAnalytics"] = array(
	"name" => "统计",
	"description" => "添加腾讯统计到每一个页面",
	"version" => ESOTALK_VERSION,
	"author" => "esoTalk Team",
	"authorEmail" => "support@esotalk.org",
	"authorURL" => "http://esotalk.org",
	"license" => "GPLv2",
	"dependencies" => array(
		"esoTalk" => "1.0.0g4"
	)
);


class ETPlugin_GoogleAnalytics extends ETPlugin {

	public function init()
	{
		ET::define("message.trackingIdHelp", "Get your Tracking ID by going into the <em>Administration</em> section for your Google Analytics Property and selecting <em>Property Settings</em>.");
	}

	/**
	 * Add the Google Analytics tracking code to the <head> of every page.
	 *
	 * @return void
	 */
	public function handler_init($sender)
	{
		if ($trackingId = C("GoogleAnalytics.trackingId")) {
			$sender->addToHead("<script type='text/javascript' src='//tajs.qq.com/stats?sId=$trackingId' charset='UTF-8'></script>
			");
		}
	}

	// Construct and process the settings form.
	public function settings($sender)
	{
		// Set up the settings form.
		$form = ETFactory::make("form");
		$form->action = URL("admin/plugins/settings/GoogleAnalytics");
		$form->setValue("trackingId", C("GoogleAnalytics.trackingId"));

		// If the form was submitted...
		if ($form->validPostBack()) {

			// Construct an array of config options to write.
			$config = array();
			$config["GoogleAnalytics.trackingId"] = $form->getValue("trackingId");

			// Write the config file.
			ET::writeConfig($config);

			$sender->message(T("message.changesSaved"), "success autoDismiss");
			$sender->redirect(URL("admin/plugins"));

		}

		$sender->data("googleAnalyticsSettingsForm", $form);
		return $this->view("settings");
	}

}
