<?php
// Copyright 2014 Tristan van Bokkem

if (!defined("IN_ESOTALK")) exit;
$form = $data["form"];
?>

<div class='sheet' id='warningSheet'>
    <div class='sheetContent'>
        <?php echo $form->open(); ?>
        <h3><?php echo T("Add a Converstation Warning"); ?></h3>
        <div class='section'>
            <?php echo $form->input("warning", "textarea", array("cols" => "67", "rows" => "10")); ?><br />
            <small><?php echo T("Define the rules of a Conversation").". ".T("HTML is allowed.");  ?></small>
        </div>
        <div class='buttons'>
            <?php echo $form->saveButton("warningSave"); ?>
            <?php echo $form->cancelButton(); ?>
        </div>
        <?php echo $form->close(); ?>
    </div>
</div>
