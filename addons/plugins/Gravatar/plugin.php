<?php
// Copyright 2013 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["Gravatar"] = array(
	"name" => "Gravatar",
	"description" => "允许用户使用 Gravatar 头像",
	"version" => ESOTALK_VERSION,
	"author" => "Toby Zerner",
	"authorEmail" => "support@esotalk.org",
	"authorURL" => "http://esotalk.org",
	"license" => "GPLv2",
	"dependencies" => array(
		"esoTalk" => "1.0.0g4"
	)
);

class ETPlugin_Gravatar extends ETPlugin {

	function init()
	{
		// Override the avatar function.

		/**
		 * Return an image tag containing a member's avatar.
		 *
		 * @param array $member An array of the member's details. (email is required in this implementation.)
		 * @param string $avatarFormat The format of the member's avatar (as stored in the database - jpg|gif|png.)
		 * @param string $className CSS class names to apply to the avatar.
		 */
		function avatar($member = array(), $className = "")
		{
			// Construct the avatar path from the provided information.
			if (!empty($member["memberId"]) and !empty($member["avatarFormat"])) {
				$cdn = C("esoTalk.cdnURL");
				$file = "uploads/avatars/{$member["memberId"]}_{$member["avatarTime"]}.{$member["avatarFormat"]}";
				$url = getWebPath($file);
				return "<img src='$cdn$url' alt='{$member["memberId"]}' class='avatar $className'/>";
			} else {
				$default = C("plugin.Gravatar.default") ? C("plugin.Gravatar.default") : "mm";

				$url = "https://cdn.v2ex.com/gravatar/".md5(strtolower(trim($member["email"])))."?d=".urlencode($default)."&s=64";

				return "<img src='$url' alt='' class='avatar $className'/>";
				
			}
		}
	}



	/**
	 * Construct and process the settings form for this skin, and return the path to the view that should be
	 * rendered.
	 *
	 * @param ETController $sender The page controller.
	 * @return string The path to the settings view to render.
	 */
	public function settings($sender)
	{
		// Set up the settings form.
		$form = ETFactory::make("form");
		$form->action = URL("admin/plugins/settings/Gravatar");
		$form->setValue("default", C("plugin.Gravatar.default", "mm"));

		// If the form was submitted...
		if ($form->validPostBack("save")) {

			// Construct an array of config options to write.
			$config = array();
			$config["plugin.Gravatar.default"] = $form->getValue("default");

			if (!$form->errorCount()) {

				// Write the config file.
				ET::writeConfig($config);

				$sender->message(T("message.changesSaved"), "success autoDismiss");
				$sender->redirect(URL("admin/plugins"));

			}
		}

		$sender->data("gravatarSettingsForm", $form);
		return $this->view("settings");
	}

}
