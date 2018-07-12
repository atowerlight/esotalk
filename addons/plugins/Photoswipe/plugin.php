<?php

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["Photoswipe"] = array(
	"name" => "Photoswipe",
	"description" => "允许用户点击帖子中的图片，形成图片画廊样式，与 Upyun 结合使用",
	'version' => '0.1.0',
	'author' => 'rhyzx',
	'authorEmail' => 'rhyzix@gmail.com',
	'authorURL' => 'https://3dgundam.org',
	'license' => 'MIT',
	"priority" => "0",
	'dependencies' => array(
		'Upyun' => '0',
	  )
);

class ETPlugin_Photoswipe extends ETPlugin {

	public function handler_conversationController_renderBefore($sender){
		$PhotoswipeCSS= "<link rel=\"stylesheet\" href=\"//cdn.bootcss.com/photoswipe/4.1.2/photoswipe.min.css\">
<link rel=\"stylesheet\" href=\"//cdn.bootcss.com/photoswipe/4.1.2/default-skin/default-skin.min.css\">";
		$PhotoswipeJS = "<script src=\"//cdn.bootcss.com/photoswipe/4.1.2/photoswipe.min.js\"></script>
<script src=\"//cdn.bootcss.com/photoswipe/4.1.2/photoswipe-ui-default.min.js\"></script>		
";

		$sender->addToHead($PhotoswipeCSS);
		$sender->addToHead($PhotoswipeJS);
		$sender->addCSSFile($this->resource("photoswipe.css"));
		$sender->addJSFile($this->resource("photoswipe.js"));
	}
}
