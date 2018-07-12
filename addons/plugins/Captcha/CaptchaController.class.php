<?php

if (!defined("IN_ESOTALK")) exit;

class CaptchaController extends ETController {
  public function action_test()
  {
    $this->form = $form = ETFactory::make('form');
    $form->action = URL('captcha/test');
    $this->data('form', $form);

    if ( $form->validPostBack('captcha') ) {
      if ( ETPlugin_Captcha::verifyCode($form->getValue('captcha')) ) {
        $this->message('Success', 'success');
      } else {
        $this->message('Failed', 'warning');
      }

      $this->render('captcha/test');
    } else {
      $this->render('captcha/test');
    }
  }

  // set captcha and show image
  public function action_index($is2x = false) {
    $fontSize = 32 * ($is2x ? 2 : 1);
    $fontColor = '666666';

    $code = ETPlugin_Captcha::generateCode();
    $path = dirname(__FILE__) . '/resources';

    $background = $is2x ? "$path/bg@2x.png" : "$path/bg.png";
    $font = "$path/font.ttf";

    // set up
    list($width, $height) = getimagesize($background);
    $img = imagecreatefrompng($background);

    list($r, $g, $b) = $this->hex2rgb($fontColor);
    $color = imagecolorallocate($img, $r, $g, $b);

    // center text
    list($x1, $y1, , , $x2, $y2) = imagettfbbox($fontSize, /*angle*/0, $font, $code);
    imagettftext($img, $fontSize, /*angle*/0, $width/2 + ($x1-$x2)/2, $height/2 + ($y1-$y2)/2, $color, $font, $code);

    // invert half image
    $invert = imagecreatetruecolor($width, $height);
    imagecopy($invert, $img, 0, 0, 0, 0, $width, $height);
    imagefilter($invert, IMG_FILTER_NEGATE);
    imagecopy($img, $invert, 0, 0, 0, 0, $width/2, $height);
    imagedestroy($invert);

    // output
    header('Cache-Control: no-cache, must-revalidate');
    header('Content-Type: image/png');
    imagepng($img);
    imagedestroy($img);
  }

  // retina support
  public function action_2x()
  {
    $this->action_index(true);
  }


  private function hex2rgb($hex) {
    $value = hexdec($hex);
    return array(
      0xFF & ($value >> 0x10),
      0xFF & ($value >> 0x8),
      0xFF & $value,
    );
  }
}
