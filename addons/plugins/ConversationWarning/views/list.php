<?php
// Copyright 2014 Tristan van Bokkem

if (!defined("IN_ESOTALK")) exit;

?>
<div class='modbreak'>
    <?php if(is_object(ET::$session) and ET::$session->isAdmin()): ?>
    <div class='controls'>
        <a class='control-edit-warning' title='<?php echo T("Edit Warning"); ?>'><i class='icon-edit'></i></a>
        <a class='control-remove-warning' title='<?php echo T("Remove Warning"); ?>'><i class='icon-remove'></i></a>
    </div>
    <?php endif; ?>
    <div class='legend'><strong><?php echo T("Warning!").":"; ?></strong></div>
    <span class='warning'><?php echo nl2br($data["warning"]); ?></span>
</div>
