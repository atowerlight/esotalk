<?php
// Made by Yathish
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * The reputation controller handles the wall of reputation, to fetch top reputation members, their ranks and nearby reputation members.
 *
 * @package esoTalk
 */
class ReputationController extends ETController {

    public function action_index($orderBy = false, $start = 0)
	{
		if (!$this->allowed("esoTalk.members.visibleToGuests")) return;
		//If admin has disabled reputatoin points, break
		if(!C("plugin.Reputation.showReputationPublic")) return;
		$model = ET::getInstance("reputationModel");
		$members = $model->getReputationMembers();

		//Get rank of current member and get nearby members if rank is greater than 10
		$rank = $model->getRankOfCurrentMember(ET::$session->userId, $members);
		//Three parameters for getNearbyReputationMembers is number of members to be shown, offset, members array
		if($rank>10) $nearbyMembers = $model->getNearbyReputationMembers(10, $rank-5, $members);

		//Get top 10 reputation members
		$topMembers = $model->getTopReputationMembers(10, $members);
		
		//Pass data to view
		$this->data("topMembers",$topMembers);
		$this->data("nearbyMembers",$nearbyMembers);
		$this->data("rank",$rank);

		$this->render("reputation");
	}

}

