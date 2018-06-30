<?php
// Copyright 2011 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * Displays a sheet with a form to edit a field's details, or create a new one.
 *
 * @package esoTalk
 */

$form = $data["form"];
$field = $data["field"];
?>
<div class='sheet' id='editFieldSheet'>
<div class='sheetContent'>

<?php echo $form->open(); ?>

<h3><?php echo T($field ? "Edit Field" : "Create Field"); ?></h3>

<div class='sheetBody'>

<div class='section' id='editFieldForm'>

<ul class='form'>

<li>
<label><?php echo T("Field name"); ?></label>
<?php echo $form->input("name"); ?>
</li>

<li class='sep'></li>

<li>
<label><?php echo T("Field description"); ?></label>
<?php echo $form->input("description", "textarea"); ?>
</li>

<li class='sep'></li>

<li>
<label><?php echo T("Input type"); ?></label>
<?php echo $form->select("type", array(
	"text"       => T("Text"), 
	"textarea"   => T("Textarea"),
	"select"     => T("Select"),
	"radios"     => T("Radio buttons"),
	"checkboxes" => T("Checkboxes"),
	"member"     => T("Member reference")
)); ?>
<div class='options'>
	<?php echo $form->input("options", "textarea", array("placeholder" => T("Enter one option per line"), "style" => "height:100px")); ?>
</div>
<script>
$(function() {
	$("select[name=type]").change(function() {
		$(".options").toggle(["select", "radios", "checkboxes"].indexOf($(this).val()) != -1);
	}).change();
});
</script>
</li>

<li class='sep'></li>

<li>
<label><?php echo T("Options"); ?></label>
<div class='checkboxField'>
<label class='checkbox'><?php echo $form->checkbox("showOnPosts"); ?> <?php echo T("Show field on posts"); ?></label>
<label class='checkbox'><?php echo $form->checkbox("hideFromGuests"); ?> <?php echo T("Hide field from guests"); ?></label>
<label class='checkbox'><?php echo $form->checkbox("searchable"); ?> <?php echo T("Searchable in member list"); ?> (<code>field:value</code>)</label>
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
