<?php
if (! defined ( "IN_ESOTALK" ))
	exit ();
class ConfigAdminController extends ETAdminController {
	protected function plugin() {
		return ET::$plugins ["ConfigEditor"];
	}
	/*
	 * The index page.
	 * @return void
	 */
	public function action_index() {
		$form = ETFactory::make ( "form" );
		$form->action = URL ( "admin/config_editor/save" );
		$config = file_get_contents ( PATH_CONFIG . '/config.php' );
		if(R("loadbackup") == '1'){
			if ($c = file_get_contents ( PATH_CONFIG . '/config_backup.php' )){
				$config = $c;
				$this->message("加载了上一次的备份。", "success");
			}else{
				$this->message("找不到备份。", "error");
			}
			
		}
		$form->setValue ( "content", $config );
		$this->data ( "form", $form );
		$this->title = "编辑config.php";
		$this->render ( $this->plugin ()->view ( "admin/edit" ) );
	}
	/*
	 * Save the config.php file and the backup.
	 * @return void 
	 */
	public function action_save() {
		if (! $this->validateToken ())
			return;
		$config = @file_get_contents ( PATH_CONFIG . '/config.php' );
		$config_backup = @file_get_contents ( PATH_CONFIG . '/config_backup.php' );
		$message = '';
		if($config && $config != $config_backup){
			//保存之前备份
			if(file_force_contents(PATH_CONFIG. '/config_backup.php', $config))
				$message .= '并保存了备份。';
		}
		if($content = R("content")){
			if(file_force_contents(PATH_CONFIG. '/config.php', $content)){
                //in SAE
                if(!empty($_SERVER['HTTP_APPNAME'])){
                    $kv = new SaeKV();
					// 初始化SaeKV对象
					$kv->init();
					// 删除key-value
    				$kv->delete('site_config');
                }
				$this->message("config.php写入成功！！".$message, "success");
			}else{
				$this->message("config.php写入失败！！", "error");
            }
		}
		$this->redirect ( URL ( "admin/config_editor" ) );
	}
}
