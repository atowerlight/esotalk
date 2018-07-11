<?php

if (!defined('IN_ESOTALK')) exit;

class UpyunController extends ETController {

  public function action_index()
  {
    $this->render404(T('message.pageNotFound'));
    return false;
  }

  public function action_signature()
  {
    if ( !ET::$session->user )
    {
      $this->render404(T('message.pageNotFound'));
      return false;
    }

    $bucket = C('plugin.upyun.bucket');
    $secret = C('plugin.upyun.secret');
    $expiration = 86400; // 24h

    // TODO more configs
    $policy = array(
      'bucket' => $bucket,
      'expiration' => time() + $expiration,
      'save-key' => '/esotalk/{year}/{mon}/{day}/{filename}{.suffix}',
      'content-length-range' => '1024,12582912', // 1K - 12M
    );


    $policyBase64 = base64_encode(json_encode($policy));
    $signature = md5("$policyBase64&$secret");

    header('Cache-Control: max-age=' . $expiration - 600);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
      'bucket' => $bucket,
      'policy' => $policyBase64,
      'signature' => $signature,
    ));
  }
}
