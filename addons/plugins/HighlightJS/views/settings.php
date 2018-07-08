<?php
// Copyright 2015 Andrew Bagshaw
if (!defined("IN_ESOTALK")) exit;

$form = $data["HighlightJSForm"];
?>
<?php echo $form->open(); ?>
<div class="section">
	<ul class="form">
		<li>
			<label>自定风格</label>
			<?php echo $form->input('customstyle', 'text'); ?>
			<small><?php echo T("Enter a custom stylesheet for HighlightJS. (e.g. 'androidstudio' without the quotes)"); ?></small>
			<small><?php echo T("Do not input the extensions .min.css at the end. Just enter the name."); ?></small>
			<small><?php echo T("If no custom stylesheet is specified, the default will be used."); ?></small>
		<li>
	</ul>
</div>
<div class="buttons">
	<?php echo $form->saveButton("HighlightJSSave"); ?>
</div>
<?php echo $form->close(); ?>