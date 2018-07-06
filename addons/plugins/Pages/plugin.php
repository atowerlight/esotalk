<?php
// This file is part of esoTalk. Please see the included license file for usage information.
if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["Pages"] = array(
	"name" => "静态页面",
	"description" => "允许管理员添加静态页面",
	"version" => ESOTALK_VERSION,
	"author" => "Aleksandr Tsiolko",
	"authorEmail" => "support@esotalk.org",
	"authorURL" => "http://ifirestarter.ru",
	"license" => "MIT"
);

class ETPlugin_Pages extends ETPlugin {

	public function setup($oldVersion = "")
	{
		$structure = ET::$database->structure();
		$structure->table("page")
			->column("pageId", "int(11) unsigned", false)
			->column("title", "varchar(31)", false)
			->column("content", "text")
			->column("slug", "varchar(255) unique")
			->column("hideFromGuests", "tinyint(1)", 0)
			->column("menu", "enum('user','statistics','meta')", "user")
			->column("position", "int(11)", 0)
			->key("pageId", "primary")
			->exec(false);

		/*:-)*/
		if (!$oldVersion){
			$this->createDefaultPages();
		}
		elseif (version_compare($oldVersion, "1.0.0g4", "<")) {
			$this->createDefaultPages();
		}else{
			$this->createDefaultPages();
		}
		/*:-)*/
		return true;
	}
	
	protected function createDefaultPages()
	{
		$model = ET::getInstance("pageModel");
		$model->setData(array(
			"pageId"     => 1,
			"title"        => "关于",
			"content" => "
本论坛使用 [url=https://to.towerlight.top/eso-bbs]esoTalk 中文优化版[/url]构建。
Esotalk 是一个优美且简单的论坛软件，她让你专注于管理论坛而不是解决问题、学习解决办法。

为了保持这里的良好氛围，本站有自己的明确规则：

• 这里感激和崇尚美的事物
• 这里尊重原创
• 这里反对中文互联网上的无信息量习惯如“顶”，“沙发”，“前排”，“留名”，“路过”，“不明觉厉”
• 这里禁止发布人身攻击、仇恨、暴力、侮辱性的言辞、暴露他人隐私的“人肉贴”
• 遵守中国的法律

为了获得访问本站的最佳体验，我们推荐使用 Google Chrome 或 Mozilla Firefox 浏览器",
			"slug" => "about-page",
			"hideFromGuests"        => 0,
			"menu"=>"meta",
			"position"        => 1
		));
		$model->setData(array(
			"pageId"     => 2,
			"title"        => "许可",
			"content" => "
本论坛若无特殊说明都遵循 [url=https://creativecommons.org/licenses/by/4.0/deed.zh]署名 4.0 国际 (CC BY 4.0)[/url]

您可以自由地：
• 共享 — 在任何媒介以任何形式复制、发行本作品
• 演绎 — 修改、转换或以本作品为基础进行创作
在任何用途下，甚至商业目的。只要你遵守许可协议条款，许可人就无法收回你的这些权利。

惟须遵守下列条件：
• 署名 — 您必须给出适当的署名，提供指向本许可协议的链接，同时标明是否（对原始作品）作了修改。您可以用任何合理的方式来署名，但是不得以任何方式暗示许可人为您或您的使用背书。
• 没有附加限制 — 您不得适用法律术语或者技术措施从而限制其他人做许可协议允许的事情。

声明：
您不必因为公共领域的作品要素而遵守许可协议，或者您的使用被可适用的例外或限制所允许。
不提供担保。许可协议可能不会给与您意图使用的所必须的所有许可。例如，其他权利比如形象权、隐私权或人格权可能限制您如何使用作品。",
			"slug" => "licenses-page",
			"hideFromGuests"        => 0,
			"menu"=>"meta",
			"position"        => 2
		));
	}

	public function __construct($rootDirectory)
	{
		parent::__construct($rootDirectory);
		
		ETFactory::register("pageModel", "PageModel", dirname(__FILE__)."/PageModel.class.php");
		
		ETFactory::registerAdminController("pages", "PagesAdminController", dirname(__FILE__)."/PagesAdminController.class.php");
		ETFactory::registerController("pages", "PagesController", dirname(__FILE__)."/PagesController.class.php");
	}


	public function handler_initAdmin($sender, $menu)
	{
		$menu->add("pages", "<a href='".URL("admin/pages")."'><i class='icon-book'></i> ".T("Pages")."</a>");
	}
	
	public function handler_init($sender) 
	{
		$model = ET::getInstance("pageModel");
		$pages = $model->get();
		if($pages){
			foreach($pages as $page){
				if (ET::$session->userId) {
					$sender->addToMenu($page['menu'], $page['slug'].'-page', '<a href="'.URL("pages").'/'.$page['pageId'].'-'.$page['slug'].'">'.$page['title'].'</a>');
				} 
				elseif($page['hideFromGuests']==0){
					$sender->addToMenu($page['menu'], $page['slug'].'-page', '<a href="'.URL("pages").'/'.$page['pageId'].'-'.$page['slug'].'">'.$page['title'].'</a>');
				}				
			}
		}		
	}
	
	public function disable()
	{
		return true;
	}
	
	public function uninstall()
	{
		$structure = ET::$database->structure();
		$structure->table("page")
			->drop();
		return true;
	}
}
