<?php
if (!defined("IN_ESOTALK")) exit;
$form = $data["form"];
?>

<?php echo $form->open(); ?>
<div class='section'>
<ul class='form'>
  <li>
    <label>Bucket</label>
    <?php echo $form->input("bucket", "text"); ?>
  </li>
  <li>
    <label>Secret</label>
    <?php echo $form->input("secret", "text"); ?>
  </li>
</ul>
</div>
<div class='buttons'>
  <?php echo $form->saveButton("submit"); ?>
</div>
<?php echo $form->close(); ?>

