<?php
if (!defined('IN_ESOTALK')) exit;
$form = $data['form'];
?>
<div class="captcha">
  <img src="/captcha" srcset="/captcha/2x 2x" alt="" width="102" height="34" title="点击更换验证码" role="button">
  <?php echo $form->input('captcha', 'text', array('placeholder' => '输入验证码', 'value' => '', 'tabindex' => $data['tabindex'] )) ?>
  <?php if ($data['tips']): ?>
  <br><small>请输入图片中的验证码</small>
  <?php endif ?>
</div>
