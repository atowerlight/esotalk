<?php
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

class PagesController extends ETController {

	protected function model()
	{
		return ET::getInstance("pageModel");
	}

	protected function plugin()
	{
		return ET::$plugins["Pages"];
	}

	public function action_index($pageSlug = false)
	{
		list($pageId,$slug)  = explode('-',trim($pageSlug));
		if(!is_numeric($pageId)){
			$this->redirect(URL(""));
		}
		
		$page = $this->model()->getById((int)$pageId);

		// Stop here with a 404 header if the page wasn't found.
		if (!$page) {
			$this->render404(T("message.pageNotFound"), true);
			return false;
		}elseif(!ET::$session->userId and $page['hideFromGuests']){
			$this->render404(T("message.pageNotFound"), true);
			return false;
		}
		$this->title = $page["title"];
		
		if (strlen($page['content']) > 155) {
			$description = substr($page['content'], 0, 155) . " ...";
			$description = str_replace(array("\n\n", "\n"), " ", $description);
		}else{
			$description = $page["content"];
		}		
		$this->addToHead("<meta name='description' content='".sanitizeHTML($description)."'>");
		
		$page['content'] = ET::formatter()->init($page["content"])->format()->get();
		$this->data("page", $page);
		$this->render($this->plugin()->view("page"));
	}
}