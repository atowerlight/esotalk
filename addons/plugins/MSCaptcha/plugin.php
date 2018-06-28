<?php
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["MSCaptcha"] = array(
	"name" => "MSCaptcha",
	"description" => "Just simple captcha.",
	"version" => ESOTALK_VERSION,
	"author" => "DaVchezt",
	"authorEmail" => "chezt.v@live.com",
	"authorURL" => "http://davchezt.tumblr.com",
	"license" => "GPLv2"
);

class ETPlugin_MSCaptcha extends ETPlugin {

	public function __construct($rootDirectory)
	{
		parent::__construct($rootDirectory);
				
		ETFactory::registerController("mscaptcha", "MSCaptchaController", dirname(__FILE__)."/MSCaptchaController.class.php");
	}
	
	public function handler_init($sender)
	{
		$sender->addCSSFile($this->Resource("mscaptcha.css"));
		$sender->addJSFile($this->Resource("mscaptcha.js"));
	}

	public function handler_userController_initJoin($controller, $form)
	{
		$form->addSection("mscaptcha", T("Solve this"));

		$form->addField("mscaptcha", "mscaptcha", function($form)
		{
			return "<div class=\"mscaptcha\"><img class=\"img-mscaptcha\" src=\"".URL("mscaptcha")."\" alt=\"MSCaptcha\"><i class=\"icon-spinner mscaptcha-loader\" style=\"display:none;\"> loading...</i><br /><a id=\"mscaptcha-refresh\" href=\"#\" class=\"button\"><i class=\"icon-refresh\"></i></a>".$form->input("mscaptcha")."</div>";
		},
		function($form, $key, &$data)
		{
			if (ET::$session->get('mscaptcha') != $form->getValue($key)) {
				ET::$session->store('inputmscaptha', $form->getValue($key));
                if(!($err = ET::$session->get('capthaerrnum'))) $err = 0;
                $err++;
                ET::$session->store('capthaerrnum', $err);
				$form->error($key, T("Invalid!, You need calculator? :D"));
			}
		});
	}
		
}
