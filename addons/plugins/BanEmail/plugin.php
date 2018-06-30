<?php

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["BanEmail"] = array(
    "name"        => "禁止电子邮件地址",
    "description" => "禁止用户使用某些电子邮件服务",
    "version"     => "1.0.1",
    "author"      => "saturngod",
    "authorEmail" => "saturngod@gmail.com",
    "authorURL"   => "http://github.com/saturngod",
    "license"     => "MIT"
);

class ETPlugin_BanEmail extends ETPlugin {
	public function handler_userController_initJoin($controller, $form)
	{

		$form->addField("BanEmail", "BanEmail", function($form) {},function($form, $key, &$data)
		{

			$email = $form->getValue("email");

			$found = false;
			$error = "";

      $lists = trim(C("plugin.BanEmail.emails"));

      if($lists == "") {
        return;
      }

      $ban_emails = explode(",",$lists);

			foreach ($ban_emails as $search_mail) {

        $address = "@".trim($search_mail);

				$pos = strpos($email,$address);
				if ($pos !== false) {
					$found = true;
					$error = $search_mail . " is not allow to register.";
					break;
				}
			}
			if($found) {
				$form->error("email",$error);
			}


		});
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
    $form->action = URL("admin/plugins/settings/BanEmail");
    $form->setValue("emails", C("plugin.BanEmail.emails"));

    // If the form was submitted...
    if ($form->validPostBack("BanEmailSave")) {

      // Construct an array of config options to write.
      $config = array();
      $config["plugin.BanEmail.emails"] = $form->getValue("emails");

      if (!$form->errorCount()) {

        // Write the config file.
        ET::writeConfig($config);

        $sender->message(T("message.changesSaved"), "success autoDismiss");
        $sender->redirect(URL("admin/plugins"));

      }
    }

    $sender->data("BanemailSettingsForm", $form);
    return $this->view("settings");
  }
}
