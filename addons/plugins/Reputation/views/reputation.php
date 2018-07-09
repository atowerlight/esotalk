<?php
// Created by Yathish
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * Displays the top reputation members sheet.
 *
 * @package esoTalk
 */
?>
<div class='sheetContent'><h1>积分排行榜</h1>
最高的10位用户
</div>
<div class='sheet' id='onlineSheet'>
<div class='sheetContent'>

<h3 style="text-align:center"><?php echo T("Top 10 members"); ?></h3>

<div class='sheetBody'>

<div class='section' id='onlineList'>
<ul class='list'>

<?php foreach ($data["topMembers"] as $member): ?>
<li>
<span class='action'>
<?php echo $member["rank"], ". ", $member["avatar"], " ", memberLink($member["memberId"], $member["username"]), " +", number_format($member["reputationPoints"]), " 积分"; ?>
</span>
</li>
<?php endforeach; ?>

</ul>

</div>

</div>

</div>
</div>


<div class='sheet' id='onlineSheet'>
<div class='sheetContent'>

<h3 style="text-align:center"><?php echo T("Members nearby your rank"); ?></h3>

<div class='sheetBody'>

<div class='section' id='onlineList'>
<ul class='list'>

<?php if($data["nearbyMembers"]): ?>

<?php foreach ($data["nearbyMembers"] as $member): ?>
<li>
<span class='action'>
<?php echo $member["rank"], ". ", $member["avatar"], " ", memberLink($member["memberId"], $member["username"]), " +", number_format($member["reputationPoints"]), " Reputation Points"; ?>
</span>
</li>
<?php endforeach; ?>

<?php else: echo "祝贺你！你已经是前10名了！"; ?>
<?php endif; ?>

</ul>

</div>

</div>

</div>
</div>