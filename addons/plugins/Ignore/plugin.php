<?php
// Copyright 2014 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["Ignore"] = array(
	"name" => "忽略发言",
	"description" => "允许用户忽略某些用户的发言，仅作用于关注的帖子",
	"version" => ESOTALK_VERSION,
	"author" => "Toby Zerner",
	"authorEmail" => "support@esotalk.org",
	"authorURL" => "http://esotalk.org",
	"license" => "GPLv2",
	"dependencies" => array(
		"esoTalk" => "1.0.0g4"
	)
);

class ETPlugin_Ignore extends ETPlugin {

	// Setup: add a follow column to the member_channel table.
	public function setup($oldVersion = "")
	{
		$structure = ET::$database->structure();
		$structure->table("member_member")
			->column("ignored", "bool", 0)
			->exec(false);

		return true;
	}

	public function init()
	{
		ET::define("message.noIgnoredMembers", "You haven't ignored any members. To ignore a member, go to their profile and choose <strong>Controls &rarr; Ignore member</strong>.");
	}

	public function handler_memberController_initProfile($sender, &$member, $panes, $controls, $actions)
	{
		if (!ET::$session->user or $member["memberId"] == ET::$session->userId) return;

		$controls->separator(0);
	 	$controls->add("ignore", "<a href='".URL("member/ignore/".$member["memberId"]."?token=".ET::$session->token."&return=".urlencode(ET::$controller->selfURL))."' id='ignoreLink'><i class='icon-eye-close'></i>".T($member["ignored"] ? "Unignore member" : "Ignore member")."</a>", 0);
	}

	// Add an action to toggle the ignoring status of a member.
	public function action_memberController_ignore($controller, $memberId = "")
	{
		if (!ET::$session->user or !$controller->validateToken()) return;

		// Make sure the member that we're trying to ignore exists.
		if (!ET::SQL()->select("memberId")->from("member")->where("memberId", (int)$memberId)->exec()->numRows()) return;

		// Work out if we're already ignored or not, and switch to the opposite of that.
		$ignored = !ET::SQL()
			->select("ignored")
			->from("member_member")
			->where("memberId1", ET::$session->userId)
			->where("memberId2", (int)$memberId)
			->exec()
			->result();

		// Write to the database.
		ET::memberModel()->setStatus(ET::$session->userId, $memberId, array("ignored" => $ignored));

		// Redirect back to the member profile.
		$controller->redirect(R("return", URL("member/".$memberId)));
	}

	protected function getIgnored()
	{
		// Get a list of all the members that the user has ignored.
		$result = ET::SQL()
			->select("memberId2")
			->from("member_member")
			->where("memberId1", ET::$session->userId)
			->where("ignored", 1)
			->exec();
		$ignoredIds = array_keys($result->allRows("memberId2"));

		return $ignoredIds;
	}

	public function handler_postModel_getPostsAfter($sender, &$posts)
	{
		$ignoredIds = $this->getIgnored();

		foreach ($posts as $k => &$post) {
			if (in_array($post["memberId"], $ignoredIds)) unset($posts[$k]);
		}
	}

	public function handler_searchModel_afterGetResults($sender, &$results)
	{
		$ignoredIds = $this->getIgnored();

		foreach ($results as &$result) {
			if (in_array($result["lastPostMemberId"], $ignoredIds)) $result["unread"] = 0; 
		}
	}

	public function handler_settingsController_initProfile($controller, $panes)
	{
	    $panes->add("ignored", "<a href='".URL("settings/ignored")."'>".T("Ignored")."</a>");
	}

	public function action_settingsController_ignored($controller)
	{
	    // The profile method sets up the settings page and returns the member's details.
	    // The argument is the name of the currently-active pane.
	    if (!($member = $controller->profile("ignored"))) return;

	    $ignoredIds = $this->getIgnored();

	    if ($ignoredIds) {
		    $sql = ET::SQL();
		    $sql->where("m.memberId IN (:memberIds)")
		    	->bind(":memberIds", $ignoredIds)
		    	->orderBy("m.username ASC");
		    $members = ET::memberModel()->getWithSQL($sql);
		    $controller->data("ignored", $members);
		}

		$controller->addCSSFile($this->resource("ignore.css"));
	    $controller->renderProfile($this->view("ignored"));
	}

}
