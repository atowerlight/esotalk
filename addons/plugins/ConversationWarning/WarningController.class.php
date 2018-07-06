<?php
// Copyright 2014 Tristan van Bokkem

if (!defined("IN_ESOTALK")) exit;

class WarningController extends ETController {

	protected function plugin()
	{
		return ET::$plugins["ConversationWarning"];
	}

	protected function model()
	{
		return ET::getInstance("warningModel");
	}

	public function action_index($conversationId = 0)
	{
		// Get the existing warning.
		$model = ET::getInstance("warningModel");
		$result = $model->getWarning($conversationId);
		$warning = $result->result();

		// Set up the form.
		$form = ETFactory::make("form");
		$form->addHidden("conversationId", $conversationId);
		$form->setValue("warning", $warning);
		$form->action = URL("warning");

		// Was the save button pressed?
		if ($form->validPostBack("warningSave")) {

			// Get the conversationId and warning values
			$conversationId = $form->getValue("conversationId");
			$warning = $form->getValue("warning");

			// Update the conversation warning column with the warning.
			$model = $this->model();
			$model->update($conversationId, $warning);

			if ($model->errorCount()) {

				// If there were errors, pass them on to the form.
				$form->errors($model->errors());
			} else {

				// Otherwise, send the admin a success message.
				ET::$controller->message(T("Warning successfully added."), "success autoDismiss");

				// And redirect back to the conversation page.
				$this->redirect(URL("conversation/".$conversationId));
			}
		}

		$this->data("form", $form);
		$this->responseType = RESPONSE_TYPE_VIEW;
		$this->render($this->plugin()->view("add"));
	}

	public function action_remove($conversationId)
	{
		// We can't do this if we're not admin.
		if (!ET::$session->isAdmin() or !$this->validateToken()) return false;

		// Remove the warning.
		$model = ET::getInstance("warningModel");
		$result = $model->update($conversationId);
		$warning = $result->result();

		return $warning;
	}
}
