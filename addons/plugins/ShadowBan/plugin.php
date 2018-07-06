<?php
// Copyright 2014 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["ShadowBan"] = array(
	"name" => "黑名单",
	"description" => "允许用户将某些用户列入黑名单",
	"version" => ESOTALK_VERSION,
	"author" => "Toby Zerner",
	"authorEmail" => "support@esotalk.org",
	"authorURL" => "http://esotalk.org",
	"license" => "GPLv2",
	"dependencies" => array(
		"esoTalk" => "1.0.0g4"
	)
);

class ETPlugin_ShadowBan extends ETPlugin {

	// Setup: add columns to the member and post tables.
	public function setup($oldVersion = "")
	{
		$structure = ET::$database->structure();
		$structure->table("member")
			->column("shadowBanned", "bool", 0)
			->exec(false);

		$structure->table("post", "MyISAM")
			->column("shadowDeleted", "bool", 0)
			->exec(false);

		return true;
	}

	public function handler_memberController_initProfile($sender, &$member, $panes, $controls, $actions)
	{
		$model = ET::memberModel();

		// If we can suspend the member, then we can shadow-ban them too.
		if ($model->canSuspend($member)) {
		 	$controls->add("shadowBan", "<a href='".URL("member/shadowBan/".$member["memberId"]."?token=".ET::$session->token."&return=".urlencode(ET::$controller->selfURL))."' id='shadowBanLink'><i class='icon-adjust'></i>".T($member["shadowBanned"] ? "Un-shadow ban" : "Shadow ban")."</a>", array("before" => "suspend"));
		}
	}

	public function action_memberController_shadowBan($controller, $memberId = "")
	{
		if (!($member = $controller->getMember($memberId))) return;
		if (!ET::$session->user or !$controller->validateToken()) return;

		// If we don't have permission to shadow-ban the member, throw an error.
		if (!ET::memberModel()->canSuspend($member)) {
			$this->renderMessage(T("Error"), T("message.noPermission"));
		 	return;
		}

		// Write to the database.
		ET::memberModel()->updateById($member["memberId"], array("shadowBanned" => !$member["shadowBanned"]));

		// Redirect back to the member profile.
		$controller->redirect(R("return", URL("member/".$memberId)));
	}

	public function handler_postModel_getPostsBefore($sender, $sql)
	{
		$sql->select("m.shadowBanned", "shadowBanned");
	}

	public function handler_conversationController_formatPostForTemplate($sender, &$formatted, $post, $conversation)
	{
		// If the post hasn't been deleted, but its author has been shadow-
		// banned, then hide it from everyone but the author.
		if (!$post["deleteTime"]) {
			if ($conversation["startMemberId"] != ET::$session->userId && $post["shadowBanned"]) {
				$post["deleteTime"] = $post["time"];
				$formatted = $sender->formatPostForTemplate($post, $conversation);
				if ($conversation["canModerate"]) {
					$formatted["controls"] = array("<span>This member has been shadow-banned</span>");
				}
			}
		}

		// If the post has been deleted, then we have the ability to shadow-
		// delete it.
		else {
			if ($conversation["canModerate"]) {
				addToArray($formatted["controls"], "<a href='".URL("conversation/shadowDelete/".$post["postId"]."?token=".ET::$session->token)."' title='".T($post["shadowDeleted"] ? "Un-shadow Delete" : "Shadow Delete")."' class='control-shadowDelete'><i class='icon-".($post["shadowDeleted"] ? "circle" : "adjust")."'></i></a>");
			}

			// If it has been shadow-deleted or its author shadow-banned, then
			// hide it from everyone but the author.
			if ($conversation["startMemberId"] == ET::$session->userId && ($post["shadowDeleted"] || $post["shadowBanned"])) {
				$post["deleteTime"] = null;
				$formatted = $sender->formatPostForTemplate($post, $conversation);
			}
		}
	}

	public function action_conversationController_shadowDelete($sender, $postId)
	{
		$conversation = ET::conversationModel()->getByPostId($postId);

		if (!$conversation or !$sender->validateToken()) return;

		// Stop here with an error if the user isn't allowed to shadow delete the post.
		if (!$conversation["canModerate"]) {
			$sender->renderMessage(T("Error"), T("message.noPermission"));
			return false;
		}

		$model = ET::postModel();
		$post = $model->getById($postId);
		$model->updateById($postId, array("shadowDeleted" => !$post["shadowDeleted"]));

		redirect(URL(R("return", postURL($postId))));
	}

	public function handler_conversationModel_addReplyBeforeCreateActivity($sender, $conversation, $postId)
	{
		// Prevent notifications from being sent when a reply is made if the
		// member is shadow-banned.
		if (ET::$session->user["shadowBanned"]) {
			return $postId;
		}
	}

	public function handler_activityModel_getActivityBefore($sender, $member, $activity, $posts)
	{
		// If the member is shadow-banned, hide all of their posts from their
		// activity stream.
		if ($member["shadowBanned"] && $member["memberId"] != ET::$session->userId) {
			$posts->where("0=1");
		}

		// Hide any shadow-deleted posts.
		$posts->where("p.shadowDeleted=0");
	}

	// public function handler_postModel_getPostsAfter($sender, &$posts)
	// {
	// 	foreach ($posts as $k => &$post) {
	// 		if ($post["shadowBanned"] or $post["shadowDeleted"]) {
	// 			unset($posts[$k]);
	// 		}
	// 	}
	// }

	// public function handler_searchModel_beforeGetResults($controller, $sql)
	// {
	// 	$sql->select("lpm.shadowBanned OR lp.shadowDeleted", "shadowLastPost")
	// 	    ->from("post lp", "c.conversationId=lp.conversationId AND c.lastPostTime=lp.time AND c.lastPostMemberId=lp.memberId", "left");
	// }

	// public function handler_searchModel_afterGetResults($sender, &$results)
	// {
	// 	foreach ($results as &$result) {
	// 		if ($result["shadowLastPost"]) {
	// 			$result["unread"] = max(0, $result["unread"] - 1);
	// 		}
	// 	}
	// }
}
