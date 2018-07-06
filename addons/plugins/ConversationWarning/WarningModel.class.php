<?php
// Copyright 2014 Tristan van Bokkem

if (!defined("IN_ESOTALK")) exit;

class WarningModel extends ETModel {

    public function __construct()
    {
        parent::__construct("conversation");
    }

    // Function to update the warning column.
	public function update($conversationId, $warning = NULL)
	{
		return ET::SQL()
			->update("conversation")
			->set("warning", $warning)
			->where("conversationId = :conversationId")
            ->bind(":conversationId", $conversationId, PDO::PARAM_INT)
			->exec();
	}

    // Function to retrieve an existing warning.
	public function getWarning($conversationId)
	{
		return ET::SQL()
			->select("warning")
			->from("conversation")
            ->where("conversationId = :conversationId")
            ->bind(":conversationId", $conversationId, PDO::PARAM_INT)
            ->exec();
	}
}
