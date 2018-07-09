<?php
// Reputation points experiment. Started on July 31, 3015

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["Reputation"] = array(
	"name" => "积分",
	"description" => "论坛积分插件",
	"version" => ESOTALK_VERSION,
	"author" => "Yathish Dhavala",
	"authorEmail" => "support@esotalk.org",
	"authorURL" => "http://esotalk.org",
	"license" => "GPLv2",
	"dependencies" => array(
		"esoTalk" => "1.0.0g4"
	)
);

class ETPlugin_Reputation extends ETPlugin {

	// Setup: add a 'reputationPoints' column to the member table.
	public function setup($oldVersion = "")
	{
		$structure = ET::$database->structure();
		$structure->table("member")
			->column("reputationPoints", "int", 0)
			->key("reputationPoints")
			->exec(false);

		return true;
	}

	public function __construct($rootDirectory)
	{
		parent::__construct($rootDirectory);

		ETFactory::register("reputationModel", "ReputationModel", dirname(__FILE__)."/ReputationModel.class.php");
		ETFactory::registerController("reputation", "ReputationController", dirname(__FILE__)."/ReputationController.class.php");
	}

	// Add some default language definitions.
	public function handler_init($sender)
	{
		//If reputation points is not enabled by Admin, break
		if(!C("plugin.Reputation.showReputationPublic")) return;

		//ET::define("message.reputationConversationStarted", "Way to go! You just earned reputation for starting a conversation!");
		//ET::define("message.reputationReplyToConversation", "Hurray! You just earned reputation for posting a reply! <br> Start an interesting converstion for more.");

		//Add reputation points to the header (top bar)
		$reputationMenu = "+ ".T(number_format(ET::$session->user["reputationPoints"]). " RP");
		$reputationMenu = "<a href='".URL("reputation")."'>$reputationMenu</a>";
		$sender->addToMenu("user", "reputation", $reputationMenu, 1);
		return;
	}

	//Retrieve reputation points if enabled and exist
	public function handler_postModel_getPostsBefore($controller, $sql)
	{
		if(!C("plugin.Reputation.showReputationPublic")) return;
	    $sql->select("m.reputationPoints", "reputationPoints");
	}

	public function handler_conversationController_formatPostForTemplate($sender, &$formatted, $post, $conversation)
	{
		if ($post["deleteTime"]) return;
		if(!C("plugin.Reputation.showReputationPublic")) return;
		// Show reputation points next to username on every post
		$postMemberReputation = "+ ".$post["reputationPoints"]." RP";
		$postMemberReputation = "<a href='".URL("reputation")."' class = 'time' title='Reputation Points'>$postMemberReputation</a>";
		$formatted["info"][] = $postMemberReputation;
	}

	public function settings($sender)
	{
		// Set up the settings form. Set some default values for the first time.
		$form = ETFactory::make("form");
		$form->action = URL("admin/plugins/settings/Reputation");
		$form->setValue("showReputationPublic", C("plugin.Reputation.showReputationPublic", "0"));
		$form->setValue("conversationStartRP", C("plugin.Reputation.conversationStartRP", "10"));
		$form->setValue("getReplyRP", C("plugin.Reputation.getReplyRP", "5"));
		$form->setValue("viewsRP", C("plugin.Reputation.viewsRP", "0"));
		$form->setValue("likesRP", C("plugin.Reputation.likesRP", "5"));
		$form->setValue("replyRP", C("plugin.Reputation.replyRP", "5"));
		$form->setValue("newReputationUpdate", C("plugin.Reputation.newReputationUpdate", "0"));

		// If the form was submitted...
		if ($form->validPostBack("reputationSave")) {

			// Construct an array of config options to write.
			$config = array();
			$config["plugin.Reputation.showReputationPublic"] = $form->getValue("showReputationPublic");
			$config["plugin.Reputation.conversationStartRP"] = $form->getValue("conversationStartRP");
			$config["plugin.Reputation.getReplyRP"] = $form->getValue("getReplyRP");
			$config["plugin.Reputation.replyRP"] = $form->getValue("replyRP");
			$config["plugin.Reputation.viewsRP"] = $form->getValue("viewsRP");
			$config["plugin.Reputation.likesRP"] = $form->getValue("likesRP");
			$config["plugin.Reputation.newReputationUpdate"] = $form->getValue("newReputationUpdate");

			// Update reputatoin ponits in databse according to new formula
			if(C("plugin.Reputation.newReputationUpdate")==1) {
				$this->updateNewReputation(C("plugin.Reputation.replyRP"),C("plugin.Reputation.conversationStartRP"),C("plugin.Reputation.viewsRP"),C("plugin.Reputation.likesRP"),C("plugin.Reputation.getReplyRP"));
				$config["plugin.Reputation.newReputationUpdate"] = 0;
			}

			if (!$form->errorCount()) {

				// Write the config file.
				ET::writeConfig($config);

				$sender->message(T("message.changesSaved"), "success autoDismiss");
				$sender->redirect(URL("admin/plugins"));

			}
		}

		$sender->data("reputationSettingsForm", $form);
		return $this->view("settings");
	}

	// When a reply is posted, give reputation points to conversation starter and the member who replied.
	public function handler_conversationModel_addReplyAfter($sender, $conversation, $postId, $content)
	{
		
		//If a saved conversation is being started for first time, give reputation points for starting a conversation.
		$user = ET::$session->userId;
		if($conversation["countPosts"]==1)
		{
			$points = "reputationPoints + ".C("plugin.Reputation.conversationStartRP");
			$this->updateReputationPoints($points, $user);
			return;
		}
		
		//Give reputation points to member who replied.
		$points = "reputationPoints + ".C("plugin.Reputation.replyRP");
		$this->updateReputationPoints($points, $user);
		//ET::$controller->message(T("message.reputationReplyToConversation"), "success autoDismiss");

		//Give reputation points to conversation starter (Ignore if its the same member) for getting a reply.
		if(ET::$session->userId!=$conversation["startMemberId"]) 
		{
			$points = "reputationPoints + ".C("plugin.Reputation.getReplyRP");
			$this->updateReputationPoints($points, $conversation["startMemberId"]);
			return;
		}
	}

	// Give reputation points for starting a conversation.
	public function handler_conversationModel_createAfter($sender, $conversation, $postId, $content)
	{
		// No points if you're just saving a draft
		if($conversation["countPosts"]==0) return;
		
		//Give reputation points to member who started the conversation.
		$points = "reputationPoints + ".C("plugin.Reputation.conversationStartRP");
		$user = ET::$session->userId;
		$this->updateReputationPoints($points, $user);
		return;
	}

	// Reputation points to post member if liked by someone else
	public function handler_conversationController_like_after($sender, $postId = false)
	{
		$post = ET::postModel()->getById($postId);
		$points = "reputationPoints + ".C("plugin.Reputation.likesRP");
		//Do not update RP if member is liking self post 
		if($_SESSION['userId']!=$post["memberId"])	$this->updateReputationPoints($points, $post["memberId"]);
		return;
	}
	
	// Remove reputation points to post member if un-liked
	public function handler_conversationController_unlike_after($sender, $postId = false)
	{
		$post = ET::postModel()->getById($postId);
		$points = "reputationPoints - ".C("plugin.Reputation.likesRP");
		if($points<0) $points = 0;
		//Do not update RP if member is un-liking self post 
		if($_SESSION['userId']!=$post["memberId"])	$this->updateReputationPoints($points, $post["memberId"]);
		return;
	}

	// When we load the conversation index, conversation view count is increased. Also give reputation points.
	public function handler_conversationController_conversationIndexDefault($sender, $conversation)
	{
		if ($conversation["startMemberId"] == ET::$session->userId) return;
		
		$points = "reputationPoints + ".C("plugin.Reputation.viewsRP");
		$user = $conversation["startMemberId"];
		$this->updateReputationPoints($points, $user);
	}

	// Update reputation points in database
	public function updateReputationPoints($points, $user)
	{
		ET::SQL()
			->update("member")
			->set("reputationPoints", $points, false)
			->where("memberId", $user)
			->exec();
		return;
	}

	//Update reputation points in databse according to new formula
	public function updateNewReputation($countPostsValue,$countConversationsValue,$viewsValue,$likesValue,$repliesValue)
	{
		$database_prefix = C("esoTalk.database.prefix");
		$table_member = $database_prefix."member a";
		$table_like = $database_prefix."like d";
		$table_conversation = $database_prefix."conversation b";
		$table_post = $database_prefix."post c";
		$query = ET::$database->query("SELECT (((a.countPosts-a.countConversations)*$countPostsValue)+(a.countConversations*$countConversationsValue)+(b.views*$viewsValue)+(CASE WHEN d.postId=c.postId AND a.memberId=c.memberId THEN d.postId*$likesValue ELSE 0 END) + (CASE WHEN a.memberId=b.startMemberId THEN b.countPosts*$repliesValue ELSE 0 END)) as newRP, a.memberId FROM $table_member, $table_conversation, $table_post, $table_like GROUP BY a.memberId");
		$rowsKeyed = $query->allRows();
		
		foreach ($rowsKeyed as $member)
		{
			$this->updateReputationPoints($member["newRP"],$member["memberId"]);
		}
	}

}
