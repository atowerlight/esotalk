<?php
// Copyright 2014 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

class PageModel extends ETModel {

	public function __construct()
	{
		parent::__construct("page", "pageId");
	}

	public function getWithSQL($sql)
	{
		return $sql
			->select("f.*")
			->from("page f")
			->orderBy("f.position ASC")
			->exec()
			->allRows();
	}

	public function setData($values)
	{
		if (!isset($values["title"])) $values["title"] = "";
		$this->validate("title", $values["title"], array($this, "validateTitle"));
		
		if (!isset($values["menu"])) $values["menu"] = "user";
		$this->validate("menu", $values["menu"], array($this, "validateMenu"));

		if (!isset($values["slug"])) $values["slug"] = "";
		$this->validate("slug", $values["slug"], array($this, "validateSlug"));
		$values["slug"] = slug($values["slug"]);
	
		if ($this->errorCount()) return false;

		$pageId = parent::create($values);		
		return $pageId;
	}

	public function deleteById($id)
	{
		return $this->delete(array("pageId" => $id));
	}
	
	public function validateTitle($title)
	{
		if (!strlen($title)) return "empty";
	}
	
	public function validateMenu($menu)
	{
		if (!in_array($menu, array('user','statistics','meta'))) return "empty";
	}

	public function validateSlug($slug)
	{
		if (!strlen($slug)) return "empty";
		if (ET::SQL()
			->select("COUNT(pageId)")
			->from("page")
			->where("slug=:slug")
			->bind(":slug", $slug)
			->exec()
			->result() > 0)
			return "channelSlugTaken";
	}
}
