<?php
if (!defined("IN_ESOTALK")) exit;
$form = $data["form"];
?>

<?php echo $form->open(); ?>
<div class='section'>
<ul class='form'>
  <li>
    <label>API KEY</label>
    <?php echo $form->input("apiKey", "text"); ?>
    <small>请在<a href="https://akismet.com/" target="_blank">Akismet</a>申请API KEY</KEYgen></small>
  </li>
  <li>
    <label>用户发帖最少限制</label>
    <?php echo $form->input("userPostLimit", "number"); ?>
    <small>只审查发帖数小于设置的用户（留空都审查）</small>
  </li>
</ul>
</div>
<div class='buttons'>
  <?php echo $form->saveButton("submit"); ?>
  <button class='button big' type='button' onclick="akismetTestKey(this.form.apiKey.value)">测试</button>
</div>
<?php echo $form->close(); ?>

<script>
function akismetTestKey(key) {
  $.ETAjax({
    url: 'admin/plugins/settings.ajax/Akismet',
    type: "post",
    data: {apiKey: key, test: true}
  });
}
</script>
