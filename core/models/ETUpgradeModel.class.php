<?php
// Copyright 2011 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * The upgrade model provides methods to install and upgrade esoTalk's database structure and data.
 *
 * @package esoTalk
 */
class ETUpgradeModel extends ETModel {


/**
 * Check for updates to the esoTalk software. If there's a new version, and this is the first time we've heard
 * of it, create a notifcation for the current user.
 *
 * @return void
 */
public function checkForUpdates()
{
	// Save the last update check time so we won't do it again for a while.
	
	ET::writeConfig(array("esoTalk.admin.lastUpdateCheckTime" => time()));

	// If the latest version is different to what it was last time we checked...
	$info = C("esoTalk.admin.lastUpdateCheckInfo", array("version" => ESOTALK_VERSION));
	if (($package = ET::checkForUpdates()) and $package["version"] != $info["version"]) {

		// Create a notification.
		ET::activityModel()->create("updateAvailable", ET::$session->userId, null, $package);

		// Write the latest checked version to the config file.
		ET::writeConfig(array("esoTalk.admin.lastUpdateCheckInfo" => $package));
	}
}


/**
 * Define esoTalk's table structure, using the database structure class to create tables or make alterations
 * to existing tables as necessary.
 *
 * @param bool $drop Whether or not to drop existing tables before recreating them.
 * @return void
 */
protected function structure($drop = false)
{
	$structure = ET::$database->structure();

	// Activity table.
	$structure
		->table("activity")
		->column("activityId", "int(11) unsigned", false)
		->column("type", "varchar(255)")
		->column("memberId", "int(11) unsigned", false)
		->column("fromMemberId", "int(11) unsigned")
		->column("data", "tinyblob")
		->column("conversationId", "int(11) unsigned")
		->column("postId", "int(11) unsigned")
		->column("time", "int(11) unsigned")
		->column("read", "tinyint(1)", 0)
		->key("activityId", "primary")
		->key("memberId")
		->key("time")
		->key("type")
		->key("conversationId")
		->key("postId")
		->key("read")
		->exec($drop);

	// Channel table.
	$structure
		->table("channel")
		->column("channelId", "int(11) unsigned", false)
		->column("title", "varchar(31)", false)
		->column("slug", "varchar(31)", false)
		->column("description", "varchar(255)")
		->column("parentId", "int(11)")
		->column("lft", "int(11)", 0)
		->column("rgt", "int(11)", 0)
		->column("depth", "int(11)", 0)
		->column("countConversations", "int(11)", 0)
		->column("countPosts", "int(11)", 0)
		->column("attributes", "mediumblob")
		->key("channelId", "primary")
		->key("slug", "unique")
		->exec($drop);

	// Channel-group table.
	$structure
		->table("channel_group")
		->column("channelId", "int(11) unsigned", false)
		->column("groupId", "int(11)", false)
		->column("view", "tinyint(1)", 0)
		->column("reply", "tinyint(1)", 0)
		->column("start", "tinyint(1)", 0)
		->column("moderate", "tinyint(1)", 0)
		->key(array("channelId", "groupId"), "primary")
		->exec($drop);

	// Conversation table.
	$structure
		->table("conversation", "MyISAM")
		->column("conversationId", "int(11) unsigned", false)
		->column("title", "varchar(100)")
		->column("channelId", "int(11) unsigned")
		->column("private", "tinyint(1)", 0)
		->column("sticky", "tinyint(1)", 0)
		->column("locked", "tinyint(1)", 0)
		->column("countPosts", "smallint(5)", 0)
		->column("startMemberId", "int(11) unsigned", false)
		->column("startTime", "int(11) unsigned", false)
		->column("lastPostMemberId", "int(11) unsigned")
		->column("lastPostTime", "int(11) unsigned")
		->column("attributes", "mediumblob")
		->key("conversationId", "primary")
		->key(array("sticky", "lastPostTime")) // for the ordering of results
		->key("lastPostTime") // also for the ordering of results, and the last post gambit
		->key("countPosts") // for the posts gambit
		->key("startTime") // for the "order by newest" gambit
		->key("locked") // for the locked gambit
		->key("private") // for the private gambit
		->key("startMemberId") // for the author gambit
		->key("channelId") // for filtering by channel
		->key("title") // for the title gambit
		->exec($drop);

	// Group table.
	$structure
		->table("group")
		->column("groupId", "int(11) unsigned", false)
		->column("name", "varchar(31)", "")
		->column("canSuspend", "tinyint(1)", 0)
		->column("private", "tinyint(1)", 0)
		->key("groupId", "primary")
		->exec($drop);

	// Member table.
	$structure
		->table("member", "MyISAM")
		->column("memberId", "int(11) unsigned", false)
		->column("username", "varchar(31)", "")
		->column("email", "varchar(63)", false)
		->column("account", "enum('administrator','member','suspended')", "member")
		->column("confirmed", "tinyint(1)", 0)
		->column("password", "char(64)", "")
		->column("resetPassword", "char(32)")
		->column("rememberToken", "char(32)")
		->column("joinTime", "int(11) unsigned", false)
		->column("lastActionTime", "int(11) unsigned")
		->column("lastActionDetail", "tinyblob")
		->column("avatarFormat", "enum('jpg','png','gif')")
		->column("preferences", "mediumblob")
		->column("countPosts", "int(11) unsigned", 0)
		->column("countConversations", "int(11) unsigned", 0)
		->key("memberId", "primary")
		->key("username", "unique")
		->key("email", "unique")
		->key("lastActionTime")
		->key("account")
		->key("countPosts")
		->key("resetPassword")
		->key("rememberToken")
		->exec($drop);

	// Member-channel table.
	$structure
		->table("member_channel")
		->column("memberId", "int(11) unsigned", false)
		->column("channelId", "int(11) unsigned", false)
		->column("unsubscribed", "tinyint(1)", 0)
		->key(array("memberId", "channelId"), "primary")
		->exec($drop);

	// Member-conversation table.
	$structure
		->table("member_conversation")
		->column("conversationId", "int(11) unsigned", false)
		->column("type", "enum('member','group')", "member")
		->column("id", "int(11)", false)
		->column("allowed", "tinyint(1)", 0)
		->column("starred", "tinyint(1)", 0)
		->column("lastRead", "smallint(5)", 0)
		->column("draft", "text")
		->column("ignored", "tinyint(1)", 0)
		->key(array("conversationId", "type", "id"), "primary")
		->key(array("type", "id"))
		->exec($drop);

	// Member-group table.
	$structure
		->table("member_group")
		->column("memberId", "int(11) unsigned", false)
		->column("groupId", "int(11) unsigned", false)
		->key(array("memberId", "groupId"), "primary")
		->exec($drop);

	// Member-user table.
	$structure
		->table("member_member")
		->column("memberId1", "int(11) unsigned", false)
		->column("memberId2", "int(11) unsigned", false)
		->key(array("memberId1", "memberId2"), "primary")
		->exec($drop);

	// Post table.
	$structure
		->table("post", "MyISAM")
		->column("postId", "int(11) unsigned", false)
		->column("conversationId", "int(11) unsigned", false)
		->column("memberId", "int(11) unsigned", false)
		->column("time", "int(11) unsigned", false)
		->column("editMemberId", "int(11) unsigned")
		->column("editTime", "int(11) unsigned")
		->column("deleteMemberId", "int(11) unsigned")
		->column("deleteTime", "int(11) unsigned")
		->column("title", "varchar(100)", false)
		->column("content", "text", false)
		->column("attributes", "mediumblob")
		->key("postId", "primary")
		->key("memberId")
		->key(array("conversationId", "time"))
		->key(array("title", "content"), "fulltext")
		->exec($drop);

	// Search table.
	$structure
		->table("search")
		->column("type", "enum('conversations')", "conversations")
		->column("ip", "int(11) unsigned", false)
		->column("time", "int(11) unsigned", false)
		->key(array("type", "ip"))
		->exec($drop);
}


/**
 * Perform a fresh installation of the esoTalk database. Create the table structure and insert default data.
 *
 * @param array $info An array of information gathered from the installation form.
 * @return void
 */
public function install($info)
{
	// Create the table structure.
	$this->structure(true);

	// Create the administrator member.
	$member = array(
		"username" => $info["adminUser"],
		"email" => $info["adminEmail"],
		"password" => $info["adminPass"],
		"account" => "Administrator",
		"confirmed" => true
	);
	ET::memberModel()->create($member);

	// Set the session's userId and user information variables to the administrator, so that all entities
	// created below will be created by the administrator user.
	ET::$session->userId = 1;
	ET::$session->user = ET::memberModel()->getById(1);

	// Create the moderator group.
	ET::groupModel()->create(array(
		"name" => "Moderator",
		"canSuspend" => true
	));

	// Create the General Discussion channel.
	$id = ET::channelModel()->create(array(
		"title" => "默认节点",
		"slug" => slug("General Discussion")
	));
	ET::channelModel()->setPermissions($id, array(
		GROUP_ID_GUEST => array("view" => true),
		GROUP_ID_MEMBER => array("view" => true, "reply" => true, "start" => true),
		1 => array("view" => true, "reply" => true, "start" => true, "moderate" => true)
	));

	// Create the Staff Only channel.
	$id = ET::channelModel()->create(array(
		"title" => "管理员节点",
		"slug" => slug("Staff Only")
	));
	ET::channelModel()->setPermissions($id, array(
		1 => array("view" => true, "reply" => true, "start" => true, "moderate" => true)
	));

	// Set the flood control config setting to zero so that we can create two conversations in a row.
	ET::$config["esoTalk.conversation.timeBetweenPosts"] = 0;

	// Create a welcome conversation.
	ET::conversationModel()->create(array(
		"title" => "欢迎来到 ".$info["forumTitle"]."!",
		"content" => "[b]欢迎来到 ".$info["forumTitle"]."![/b]\n\n".$info["forumTitle"]." 使用 [url=https://to.towerlight.top/eso-bbs]esoTalk 中文优化版[/url]构建。\n\nEsotalk 是一个优美且简单的论坛软件，她让你专注于管理论坛而不是解决问题、学习解决办法。\n\n请随意的修改或者删除这个帖子。\n\n希望你能愉快的使用 esoTalk 中文优化版",
		"channelId" => 1
	));

	// Create a helpful private conversation with the administrator.
	ET::conversationModel()->create(array(
		"title" => "管理员情看，这里有几个小技巧",
		"content" => "你好 {$info["adminUser"]}, 非常高兴的告诉你，你的 esoTalk 已经安装完成。\n\n这篇文章只有你能看到，你可以随意的删除或者修改。\n\n你的论坛已经整装待发，在这之前你需要进入后台进行一些简单的设置，以自定你的论坛。\n\n[h]更改 Logo[/h]\n\n1. 去到[url=".C("esoTalk.baseURL")."admin/settings]论坛设置界面[/url]\n2. 找到 社区顶部 一栏，点击 在顶部显示图像标志 \n3. 选择一副图片作为你的 logo。\n4. 点击 保存更改 你新加入的图片将会自动出现在页面顶部\n\n[h]更改外观[/h]\n\n1. 点击 [url=".C("esoTalk.baseURL")."admin/appearance]外观设置[/url]\n2. 设置一个你喜欢的颜色，你可以在这里得到推荐的颜色[url=https://colorhunt.co/]Color Hunt[/url]\n3. 点击 保存更改 \n\n[h]管理节点[/h]\n\n你可以自由的创建你的节点和子节点，并对他们进行权限以及外观的修改\n\n1. 点击 [url=".C("esoTalk.baseURL")."admin/channels]管理节点[/url]\n2. 点击添加节点并完成类容的填写。\n3. 点击保存修改即可添加节点.\n\n[h]遇到问题[/h]\n\n如果你遇到问题请到[url=https://to.towerlight.top/eso-bbs]esoTalk 中文优化版讨论区[/url]",
		"channelId" => 1
	), array(array("type" => "member", "id" => 1)));

	// All done!
}


/**
 * Perform an upgrade to ensure that the database is up-to-date.
 *
 * @param string $currentVersion The version we are upgrading from.
 * @return void
 */
public function upgrade($currentVersion = "")
{
	// 1.0.0g5:
	// - Drop the cookie table
	// - Write config to enable persistence cookies. These are disabled by
	//   default in g5 because otherwise the ETSession class will encounter
	//   a fatal error (rememberToken column doesn't exist) before reaching
	//   the upgrade script.
	if (version_compare($currentVersion, "1.0.0g5", "<")) {
		ET::$database->structure()->table("cookie")->drop();
		ET::writeConfig(array("esoTalk.enablePersistenceCookies" => true));
	}

	// 1.0.0g4:
	// - Rename the 'confirmedEmail' column on the members table to 'confirmed'
	// - Rename the 'muted' column on the member_conversation table to 'ignored'
	if (version_compare($currentVersion, "1.0.0g4", "<")) {
		ET::$database->structure()->table("member")->renameColumn("confirmedEmail", "confirmed");
		ET::$database->structure()->table("member_conversation")->renameColumn("muted", "ignored");
	}

	// Make sure the application's table structure is up-to-date.
	$this->structure(false);

	// Perform any custom upgrade procedures, from $currentVersion to ESOTALK_VERSION, here.

	// 1.0.0g3:
	/// - Re-calculate all conversation post counts due to a bug which could get them un-synced
	if (version_compare($currentVersion, "1.0.0g3", "<")) {
		ET::SQL()
			->update("conversation c")
			->set("countPosts", "(".ET::SQL()->select("COUNT(*)")->from("post p")->where("p.conversationId=c.conversationId").")", false)
			->exec();
	}
}

}
