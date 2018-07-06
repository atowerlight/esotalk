<?php

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["EmoticonsPlus"] = array(
	"name" => "表情-增强版",
	"description" => "添加表情图片到帖子中，并添加表情选择器到编辑器中",
	"version" => "1.0",
	"author" => "Ramouch0 (based on esotalk's version)",
	"authorEmail" => "support@esotalk.org",
	"authorURL" => "http://esotalk.org",
	"license" => "GPLv2"
);

class ETPlugin_EmoticonsPlus extends ETPlugin {

	private $icons;
    private $iscon;

/**
 * Class constructor.
 *
 * @return void
 */
public function __construct($rootDirectory)
{
	parent::__construct($rootDirectory);
	$this->icons = array();
	$this->icons[":)"] = "background-position:0 0";
	$this->icons[":-)"] = "background-position:0 0";
	$this->icons["=)"] = "background-position:0 0";
	$this->icons[":-D"] = "background-position:0 -20px";
	$this->icons[":D"] = "background-position:0 -20px";
	$this->icons["=D"] = "background-position:0 -20px";
	$this->icons["^_^"] = "background-position:0 -40px";
	$this->icons["^^"] = "background-position:0 -40px";
	$this->icons[":("] = "background-position:0 -60px";
	$this->icons[":-("] = "background-position:0 -60px";
	$this->icons["=("] = "background-position:0 -60px";
	$this->icons["-_-"] = "background-position:0 -80px";
	$this->icons[";-)"] = "background-position:0 -100px";
	$this->icons[";)"] = "background-position:0 -100px";
	$this->icons["^_-"] = "background-position:0 -100px";
	$this->icons["~_-"] = "background-position:0 -100px";
	$this->icons["-_^"] = "background-position:0 -100px";
	$this->icons["-_~"] = "background-position:0 -100px";
	$this->icons["^_^;"] = "background-position:0 -120px; width:18px";
	$this->icons["^^;"] = "background-position:0 -120px; width:18px";
	$this->icons[">_<"] = "background-position:0 -140px";
	$this->icons[":/"] = "background-position:0 -160px";
	$this->icons[":-/"] = "background-position:0 -160px";
	$this->icons["=/"] = "background-position:0 -160px";
	$this->icons[":\\"] = "background-position:0 -160px";
	$this->icons["=\\"] = "background-position:0 -160px";
	$this->icons[":x"] = "background-position:0 -180px";
	$this->icons["=x"] = "background-position:0 -180px";
	$this->icons[":|"] = "background-position:0 -180px";
	$this->icons["=|"] = "background-position:0 -180px";
	$this->icons["'_'"] = "background-position:0 -180px";
	$this->icons["<_<"] = "background-position:0 -200px";
	$this->icons[">_>"] = "background-position:0 -220px";
	$this->icons["x_x"] = "background-position:0 -240px";
	$this->icons["o_O"] = "background-position:0 -260px";
	$this->icons["O_o"] = "background-position:0 -260px";
	$this->icons["o_0"] = "background-position:0 -260px";
	$this->icons["0_o"] = "background-position:0 -260px";
	$this->icons[";_;"] = "background-position:0 -280px";
	$this->icons[":'("] = "background-position:0 -280px";
	$this->icons[":O"] = "background-position:0 -300px";
	$this->icons["=O"] = "background-position:0 -300px";
	$this->icons[":o"] = "background-position:0 -300px";
	$this->icons["=o"] = "background-position:0 -300px";
	$this->icons[":-P"] = "background-position:0 -320px";
	$this->icons[":P"] = "background-position:0 -320px";
	$this->icons[":p"] = "background-position:0 -320px";
	$this->icons[":-p"] = "background-position:0 -320px";
	$this->icons["=P"] = "background-position:0 -320px";
	$this->icons[";P"] = "background-position:0 -320px";
	$this->icons[";-P"] = "background-position:0 -320px";
	$this->icons[":["] = "background-position:0 -340px";
	$this->icons["=["] = "background-position:0 -340px";
	$this->icons[":3"] = "background-position:0 -360px";
	$this->icons["=3"] = "background-position:0 -360px";
	$this->icons["._.;"] = "background-position:0 -380px; width:18px";
	$this->icons["<(^.^)>"] = "background-position:0 -400px; width:19px";
	$this->icons["(>'.')>"] = "background-position:0 -400px; width:19px";
	$this->icons["(>^.^)>"] = "background-position:0 -400px; width:19px";
	$this->icons["-_-;"] = "background-position:0 -420px; width:18px";
	$this->icons["(o^_^o)"] = "background-position:0 -440px";
	$this->icons["(^_^)/"] = "background-position:0 -460px; width:19px";
	$this->icons[">:("] = "background-position:0 -480px";
	$this->icons[">:["] = "background-position:0 -480px";
	$this->icons["._."] = "background-position:0 -500px";
	$this->icons["T_T"] = "background-position:0 -520px";
	$this->icons["XD"] = "background-position:0 -540px";
	$this->icons["('<"] = "background-position:0 -560px";
	$this->icons["B)"] = "background-position:0 -580px";
	$this->icons["XP"] = "background-position:0 -600px";
	$this->icons[":S"] = "background-position:0 -620px";
	$this->icons["=S"] = "background-position:0 -620px";
	$this->icons[">:)"] = "background-position:0 -640px";
	$this->icons[">:D"] = "background-position:0 -640px";
}

/**
 * Add an event handler to the "getEditControls" method of the conversation controller to add BBCode
 * formatting buttons to the edit controls.
 *
 * @return void
 */
public function handler_conversationController_getEditControls($sender, &$controls, $id)
{

	addToArrayString($controls, "smileys", "<a href='javascript:EmoticonAdv.showDropDown(\"$id\");void(0)' title='".T("Smileys")."' class='control-smile'><i class='icon-smile'></i></a>", 0);
}

public function handler_conversationController_renderBefore($sender)
{
	$sender->addJSFile($this->Resource("emoticon.js"));
	$sender->addCSSFile($this->Resource("emoticon.css"));
    $this->iscon = true;
    
}

public function handler_memberController_renderBefore($sender)
{
	$this->handler_conversationController_renderBefore($sender);
}

public function handler_pageEnd(){

    if($this->iscon){
        $div ="<div id='emoticonDropDown' style='display: none;'><div><ul>";
        foreach(array_unique($this->icons) as $k => $v){
            $alt = htmlentities($k, ENT_QUOTES);
            $div.="<li><a href=\"javascript:EmoticonAdv.insertSmiley('".str_replace("'","\'",$k)."');void(0)\" title=\"$alt\" style=\"$v\" class=\"emoticon\" alt=\"$alt\">\"$alt\"</a></li>";
        }
        $div .="</ul></div></div>";
        echo $div;
    }
}

public function handler_format_format($sender)
{
	$from = $to = array();
	foreach ($this->icons as $k => $v) {
		$quoted = preg_quote(sanitizeHTML($k), "/");
		$from[] = "/(?<=^|[\s.,!<>]){$quoted}(?=[\s.,!<>)]|$)/i";
		$to[] = "<span class='emoticon' style='$v'>$k</span>";
	}
	$sender->content = preg_replace($from, $to, $sender->content);
}

}
