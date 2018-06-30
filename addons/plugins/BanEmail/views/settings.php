<?php
// Copyright 2011 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * Displays the settings form for the Proto skin.
 *
 * @package esoTalk
 */

$form = $data["BanemailSettingsForm"];
?>
<?php echo $form->open(); ?>

<div class='section'>

<ul class='form'>

<li>
<label><?php echo T("Email Address"); ?></label>
<?php echo $form->input("emails", "text"); ?><br/>
使用 <b>英文逗号 (,)</b>来分隔电子邮件地址<br/>
如 : 163.com,qq.com,189.cn
</li>

</ul>

</div>

<div class='buttons'>
<?php echo $form->saveButton("BanEmailSave"); ?>
</div>

<?php echo $form->close(); ?>
