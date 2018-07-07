<?php

if (!defined('IN_ESOTALK')) exit;

ET::$pluginInfo['Akismet'] = array(
  'name' => 'Akismet',
  'description' => '使用akismet来防止垃圾信息',
  'version' => ETPlugin_Akismet::VERSION,
  'author' => 'rhyzx',
  'authorEmail' => 'rhyzix@gmail.com',
  'authorURL' => 'https://github.com/rhyzx',
  'license' => 'MIT',
  'priority' => 0,
);

class ETPlugin_Akismet extends ETPlugin {
  const VERSION = '0.2.0';
  private $message = 'spammer detected';

  public function handler_userController_initJoin($sender, $form)
  {
    // TODO? honeypot
    // $form->addSection('akismet');
    $form->addField('akismet', 'akismet', function($form)
    {
      // return $form->input('akismet', 'text');
    },
    function($form, $key, &$data) use ($sender)
    {
      if ( $this->isSpam($data['username'], $data['email'], '', 'registration') ) {
        // TODO message and i18n
        $sender->message($this->message, 'warning');
        $form->error($key, $this->message);
      }
    });
  }

  public function handler_conversationController_reply($sender, $form)
  {
    $user = ET::$session->user;

    if ( $this->userPostCountLimited()
      && $this->isSpam( $user['username'], $user['email'], $form->getValue('content') )
    ) {
      $sender->message($this->message, 'warning');
      $form->error('akismet', $this->message);
    }
  }

  public function handler_conversationController_start($sender, $form)
  {
    if (!$form->validPostBack('content')) return;
    $user = ET::$session->user;

    if ( $this->userPostCountLimited()
      && $this->isSpam( $user['username'], $user['email'], $form->getValue('content') )
    ) {
      $sender->message($this->message, 'warning');
      $form->error('akismet', $this->message);
    }
  }


  public function settings($sender)
  {
    $form = ETFactory::make('form');
    $form->action = URL('admin/plugins/settings/Akismet');

    $form->setValue('apiKey', C('plugin.akismet.apiKey'));
    $form->setValue('userPostLimit', C('plugin.akismet.userPostLimit'));

    if ($form->validPostBack('submit')) {
      $config = array();
      $config['plugin.akismet.apiKey'] = $form->getValue('apiKey');
      $config['plugin.akismet.userPostLimit'] = $form->getValue('userPostLimit');

      if (!$form->errorCount()) {
        ET::writeConfig($config);
        $sender->message(T('message.changesSaved'), 'success autoDismiss');
        $sender->redirect(URL('admin/plugins'));
      }
    } elseif ($form->validPostBack('test')) {
      $apiKey = $form->getValue('apiKey');

      if ( $this->verifyKey($apiKey) ) {
        $sender->message('verify key success', 'success autoDismiss');
      } else {
        $sender->message('verify key failed', 'warning autoDismiss');
      }
      return;
    }

    $sender->data('form', $form);
    return $this->view('settings');
  }


  private function userPostCountLimited() {
    // only check new users
    $limit = C("plugin.akismet.userPostLimit");
    if ( !is_numeric($limit) ) {
      $limit = INF; // default always check
    }

    return $limit > ET::$session->user['countPosts'];
  }

  private function isSpam($author, $email, $content = '', $type = 'comment'/*registration*/, $info = null)
  {
    $apiKey = C('plugin.akismet.apiKey');
    if (empty($apiKey)) return false; // skip if key not set

    $url = 'http://' . $apiKey . '.rest.akismet.com/1.1/comment-check';

    $headers = 'Content-type: application/x-www-form-urlencoded' . "\r\n"
      . 'User-Agent: esoTalk/' . ESOTALK_VERSION . ' | Akismet/' . self::VERSION . "\r\n"
    ;

    if ( is_null($info) ) {
      $info = $_SERVER;
    }

    if ( !empty($_SERVER['HTTP_CLIENT_IP']) ) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }

    $body = array(
      'blog' => C('esoTalk.baseURL'),
      'user_ip' => $ip,
      'user_agent' => $info['HTTP_USER_AGENT'],
      'referrer' => $info['HTTP_REFERER'],
      // 'permalink' => '',
      'comment_type' => $type,
      'comment_author' => $author,
      'comment_author_email' => $email,
      'comment_content' => $content,
    );


    $context  = stream_context_create(array('http' => array(
      'method' => 'POST',
      'header' => $headers,
      'content' => http_build_query($body),
    )));

    $result = file_get_contents($url, false, $context);

    return $result === 'true';
  }


  private function verifyKey($key) {
    $url = 'http://rest.akismet.com/1.1/verify-key';

    $headers = 'Content-type: application/x-www-form-urlencoded' . "\r\n"
      . 'User-Agent: esoTalk/' . ESOTALK_VERSION . ' | Akismet/' . self::VERSION . "\r\n"
    ;

    $body = array(
      'blog' => C('esoTalk.baseURL'),
      'key' => $key,
    );

    $context  = stream_context_create(array('http' => array(
      'method' => 'POST',
      'header' => $headers,
      'content' => http_build_query($body),
    )));

    $result = file_get_contents($url, false, $context);

    return $result === 'valid';
  }
}
