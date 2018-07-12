<?php
if (!defined("IN_ESOTALK")) exit;
$form = $data["form"];
?>

<div class="sheet">
  <div class="sheetContent">
    <h3>全自动人机区别图灵测试</h3>
    <?php echo $form->open(); ?>
    <div class="buttons">
      <button type="submit" class="submit button big">Test</button>
      <span class="captcha">
        <img src="/captcha" srcset="/captcha/2x 2x" alt="" width="102" height="34">
        <input type="text" name="captcha" value="" placeholder="输入验证码">
      </span>
    </div>
    <?php echo $form->close(); ?>
  </div>
</div>
