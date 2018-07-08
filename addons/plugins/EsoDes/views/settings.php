<?php
// Copyright 2015 Mustafa Bozkurt

if (!defined("IN_ESOTALK")) exit;

$form = $data["EsoDesSettingsForm"];
?>
<?php echo $form->open(); ?>

<div class='section'>

<ul class='form'>

<li>
<label><?php echo T("Forum Description"); ?></label>
<?php echo $form->input("forumDes", "text"); ?>
<small><?php echo T("message.forumDesHelp"); ?></small>
</li>

</ul>

</div>

<div class='buttons'>
<?php echo $form->saveButton(); ?>
</div>

<?php echo $form->close(); ?>
