<?php

if (!defined('IN_ESOTALK')) exit;

ET::$pluginInfo['Upyun'] = array(
  'name' => 'Upyun',
  'description' => '允许用户上传附件到又拍云',
  'version' => '0.1.0',
  'author' => 'rhyzx',
  'authorEmail' => 'rhyzix@gmail.com',
  'authorURL' => 'https://3dgundam.org',
  'license' => 'MIT'
);

class ETPlugin_Upyun extends ETPlugin {

  public function __construct($rootDirectory)
  {
    parent::__construct($rootDirectory);
    ETFactory::registerController('upyun', 'UpyunController', dirname(__FILE__).'/UpyunController.class.php');
  }

  public function handler_conversationController_renderBefore($sender)
  {
    $sender->addJSFile($this->resource('upyun.js'));
    $sender->addCssFile($this->resource('upyun.css'));
  }
  public function handler_conversationController_getEditControls($sender, &$controls, $id)
  {
    addToArrayString($controls, "imageup", "<a href='javascript:UPyun.imageup(\"$id\");void(0)' title='".T("文件上传")."' class='control-fixed'><i class='icon-paper-clip'></i></a>", 0);

  }
  /**
 * Add an event handler to the formatter to parse BBCode and format it into HTML.
 *
 * @return void
 */

public function handler_format_format($sender)
{
	// TODO: Rewrite BBCode parser to use the method found here:
	// http://stackoverflow.com/questions/1799454/is-there-a-solid-bb-code-parser-for-php-that-doesnt-have-any-dependancies/1799788#1799788
	// Remove control characters from the post.
	//$sender->content = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $sender->content);
	// \[ (i|b|color|url|somethingelse) \=? ([^]]+)? \] (?: ([^]]*) \[\/\1\] )

	$replacement = $sender->inline ? "[image]" : "<img src='$1' alt='-image-'/>";
	// url can be
	// - https http ftp etc
	// - protocal relative url eg. //foo.com/pic.png
	// - relative url eg. /sprite.png
	$sender->content = preg_replace("/\[upyun\]((?:\w+:\/\/|\/).*?)\[\/upyun\]/i", $replacement, $sender->content);

	// Links with display text: [url=http://url]text[/url]
	//$sender->content = preg_replace_callback("/\[url=(?!\s+)(\w{2,6}:\/\/)?([^\]]*?)\](.*?)\[\/url\]/i", array($this, "linksCallback"), $sender->content);

	// Bold: [b]bold text[/b]
	//$sender->content = preg_replace("/\[b\](.*?)\[\/b\]/si", "<b>$1</b>", $sender->content);

	// Italics: [i]italic text[/i]
	//$sender->content = preg_replace("/\[i\](.*?)\[\/i\]/si", "<i>$1</i>", $sender->content);

	// Strikethrough: [s]strikethrough[/s]
	//$sender->content = preg_replace("/\[s\](.*?)\[\/s\]/si", "<del>$1</del>", $sender->content);

	// Headers: [h]header[/h]
	//$replacement = $sender->inline ? "<b>$1</b>" : "</p><h4>$1</h4><p>";
	//$sender->content = preg_replace("/\[h\](.*?)\[\/h\]/", $replacement, $sender->content);
}


  public function settings($sender)
  {
    $form = ETFactory::make('form');
    $form->action = URL('admin/plugins/settings/Upyun');

    $form->setValue('bucket', C('plugin.upyun.bucket'));
    $form->setValue('secret', C('plugin.upyun.secret'));
    // $form->setValue('expiration', C('plugin.upyun.expiration'));

    if ($form->validPostBack('submit')) {
      $config = array();
      $config['plugin.upyun.bucket'] = $form->getValue('bucket');
      $config['plugin.upyun.secret'] = $form->getValue('secret');

      if (!$form->errorCount()) {
        ET::writeConfig($config);
        $sender->message(T('message.changesSaved'), 'success autoDismiss');
        $sender->redirect(URL('admin/plugins'));
      }
    }
    $sender->data('form', $form);
    return $this->view('settings');
  }
}
