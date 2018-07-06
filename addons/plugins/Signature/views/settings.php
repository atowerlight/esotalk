<?php
// Copyright 2014 Tristan van Bokkem

if (!defined("IN_ESOTALK")) exit;

$form = $data["SignatureSettingsForm"];
?>
<?php echo $form->open(); ?>

<div class='section'>

<ul class='form'>

<li>
    <label><strong><?php echo T("Characters"); ?></strong></label>
    <?php echo $form->input("characters", "number"); ?>
    <small><?php echo T("Enter the amount of signature characters allowed."); ?></small>
</li>

</ul>

</div>

<div class='buttons'>
<?php echo $form->saveButton(); ?>
</div>

<?php echo $form->close(); ?>
