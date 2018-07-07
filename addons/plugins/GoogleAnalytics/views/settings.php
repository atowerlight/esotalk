<?php
// Copyright 2014 Toby Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

$form = $data["googleAnalyticsSettingsForm"];
?>
<?php echo $form->open(); ?>

<div class='section'>

<ul class='form'>

<li>
<label><?php echo T("Tracking ID"); ?></label>
<?php echo $form->input("trackingId", "text"); ?>
<small><?php echo T("message.trackingIdHelp"); ?></small>
</li>

</ul>

</div>

<div class='buttons'>
<?php echo $form->saveButton(); ?>
</div>

<?php echo $form->close(); ?>
