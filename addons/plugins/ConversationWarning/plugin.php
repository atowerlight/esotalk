<?php
// Copyright 2014 Tristan van Bokkem

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["ConversationWarning"] = array(
	"name" => "回帖提醒",
	"description" => "允许用户设置回帖提醒，以提醒其他用户回帖的注意事项",
	"version" => "1.1.2",
	"author" => "Tristan van Bokkem",
	"authorEmail" => "tristanvanbokkem@gmail.com",
	"authorURL" => "http://www.bitcoinclub.nl",
	"license" => "GPLv2"
);

class ETPlugin_ConversationWarning extends ETPlugin {

	// Setup: add a 'warning' column to the conversation table.
	public function setup($oldVersion = "")
	{
		$structure = ET::$database->structure();
		$structure->table("conversation")
			->column("warning", "varchar(1000)")
			->exec(false);

		return true;
	}

	public function __construct($rootDirectory = 0)
	{
		parent::__construct($rootDirectory);

		// Register the warning model which provides convenient methods to
		// manage warning data.
		ETFactory::register("warningModel", "WarningModel", dirname(__FILE__)."/WarningModel.class.php");

		// Register the warning controller which provides an interface for
		// moderators to manage conversation warnings.
		ETFactory::registerController("warning", "WarningController", dirname(__FILE__)."/WarningController.class.php");
	}

	// Add the JavaScript and style sheet to <HEAD>.
	public function handler_conversationController_renderBefore($sender)
	{
		$sender->addCSSFile($this->resource("warning.css"));
		$sender->addJSFile($this->resource("warning.js"));
		$sender->addJSLanguage("Warning successfully removed.");
	}

	// Add the warning control button before the sitcky control button to the conversation controls.
	public function handler_conversationController_conversationIndexDefault($sender, $conversation, $controls, $replyForm, $replyControls)
	{
		if (is_object(ET::$session) and !ET::$session->isAdmin()) return;

	    $controls->add("warning", "<a href='#' id='addWarning'><i class='icon-warning-sign'></i> ".T("Warning!")."</a>", array("before" => "sticky"));

		return $controls;
	}

	// When we render the reply box, add the warning area to the bottom of it.
	public function handler_conversationController_renderReplyBox($sender, &$formatted, $conversation)
	{
		// Get the conversation warning by conversationId.
		$conversationId = $conversation["conversationId"];
		$model = ET::getInstance("warningModel");
		$result = $model->getWarning($conversationId);
		$warning = $result->result();

		// If there is a warning, append the warning div.
		if($warning) {
			$this->appendWarning($sender, $formatted, $warning);
		}
	}

	// When we render an edit post box, add the warning area to the bottom of it.
	public function handler_conversationController_renderEditBox($sender, &$formatted, $post)
	{
		// Get the conversation warning by conversationId based on the postId
		$conversation = ET::conversationModel()->getByPostId($post["postId"]);
		$conversationId = $conversation["conversationId"];

		$model = ET::getInstance("warningModel");
		$result = $model->getWarning($conversationId);
		$warning = $result->result();

		// If there is a warning, append the warning div.
		if($warning) {
			$this->appendWarning($sender, $formatted, $warning);
		}
	}

	// Get the contents of the "warning" view and append it before the editButtons div
	// and after the attachments div.
	protected function appendWarning($sender, &$formatted, $warning)
	{
		$view = $sender->getViewContents("list", array("warning" => $warning));
		if (in_array("Attachments", C("esoTalk.enabledPlugins"))) {

			// After attachments div.
			addToArray($formatted["footer"], $view, 1);

		} else {

			// Before editButtons div.
			addToArray($formatted["footer"], $view, 0);
		}
	}

	public function uninstall()
	{
		$structure = ET::$database->structure();
		$structure->table("conversation")->dropColumn("warning");
		return true;
	}
}
