<?php
// Made by Yathish
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * The reputation model provides functions for retrieving and managing member data, reputation points and rank.
 *
 * @package esoTalk
 */
class ReputationModel extends ETModel {

	public function __construct()
	{
		parent::__construct("reputation");
	}

	public function getReputationMembers()
	{
		$result = ET::SQL()
		->select("username")
		->select("email")
	    ->select("memberId")
	    ->select("reputationPoints")
	    ->from("member")
	    ->orderBy("reputationPoints DESC")
	    ->exec()
	    ->allRows();

	    //Assign ranks to all members based on reputation points
	    $rank = 1;
		foreach ($result as $k => $v) {
		$results[$k]["rank"] = $rank;
		$results[$k]["avatar"] = avatar($v, "thumb");
		$results[$k]["username"] = $result[$k]["username"];
		$results[$k]["memberId"] = $result[$k]["memberId"];
		$results[$k]["reputationPoints"] = $result[$k]["reputationPoints"];
		$rank++;
		}
		return $results;

    }

    public function getRankOfCurrentMember($memberId, $results)
    {

    	foreach ($results as $v) {
    		if($v["memberId"] == $memberId) return $v["rank"];
    	}
    }

    public function getNearbyReputationMembers($limit, $offset, $results)
    {
    	return array_slice($results, $offset, $limit);
    }

    public function getTopReputationMembers($limit, $results)
    {
    	return array_slice($results, 0, $limit);
    }

}