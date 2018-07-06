<?php
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * Displays a sheet with a form to edit a static pages, or create a new one.
 *
 * @package esoTalk
 */

$form = $data["form"];
$page = $data["page"];
?>
<div class='sheet' id='editFieldSheet'>
<div class='sheetContent'>

<?php echo $form->open(); ?>

<h3><?php echo T($page ? "Edit Page" : "Create Page"); ?></h3>

<div class='sheetBody'>

<div class='section' id='editFieldForm'>

<ul class='form'>

<li>
<label><?php echo T("Page title"); ?></label>
<?php echo $form->input("title"); ?>
</li>
<li class='sep'></li>
<li>
<label><?php echo T("Page slug"); ?></label> 
<?php echo $form->input("slug"); ?>
</li>
<li class='sep'></li>

<li>
<label><?php echo T("Page content"); ?></label>
<?php echo $form->input("content", "textarea"); ?>
</li>
<li class='sep'></li>

<li>
<label><?php echo T("Input menu"); ?></label>
<?php echo $form->select("menu", array("user" => "用户栏(头部)","statistics" => "在统计后(左下部)", "meta" => "在统计前(左下部)")); ?>
</li>
<li class='sep'></li>

<li>
<label><?php echo T("Options"); ?></label>
<div class='checkboxField'>
<label class='checkbox'><?php echo $form->checkbox("hideFromGuests"); ?> <?php echo T("Hide field from guests"); ?></label>
</div>
</li>

</ul>

</div>

</div>

<div class='buttons'>
<?php
echo $form->saveButton();
echo $form->cancelButton();
?>
</div>

<?php echo $form->close(); ?>

</div>
</div>
