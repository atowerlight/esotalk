<?php
// Copyright 2014 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;
?>

<?php if (!empty($data["ignored"])): ?>

<ul class='ignoredMembers'>

<?php foreach ($data["ignored"] as $k => $member): ?>
<li class='action'>
<a href='<?php echo URL(memberURL($member["memberId"], $member["username"])); ?>' class='name'>
<?php echo avatar($member, "thumb"); ?> 
<?php echo name($member["username"]); ?>
</a>
<a href='<?php echo URL(memberURL($member["memberId"], $member["username"], "ignore")."?token=".ET::$session->token."&return=".$this->selfURL); ?>' class='button'><?php echo T("Unignore"); ?></a>
</li>
<?php endforeach; ?>

</ul>

<?php else: ?>
<p class="help"><?php echo T("message.noIgnoredMembers"); ?></p>
<?php endif; ?>