<?php

/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class SurveyforceControllerSurveys extends JControllerAdmin {

	public $id;
	public $survs;

	public function __construct($config = array ()) {

		$app = JFactory::getApplication();

		if ($app->input->get('layout') == "preview") {
			$this->id    = $app->input->getInt("id");
			$this->survs = SurveyforceHelper::getSuveryItem($this->id);
		}

		parent::__construct($config);
	}

	public function getModel($name = 'Surveys', $prefix = 'SurveyforceModel') {
		$model = parent::getModel($name, $prefix, array ('ignore_request' => true));

		return $model;
	}

	public function add() {
		$this->setRedirect('index.php?option=com_surveyforce&task=survey.add');
	}

	public function delete() {
		// Get items to remove from the request.
		$cid  = JFactory::getApplication()->input->get('cid', array (), '', 'array');
		$tmpl = JFactory::getApplication()->input->get('tmpl');
		if ($tmpl == 'component')
			$tmpl = '&tmpl=component'; else
			$tmpl = '';

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid)) {
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			} else {
				$this->setMessage($model->getError());
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $tmpl, false));
	}

	public function edit() {
		$cid     = JFactory::getApplication()->input->get('cid', array (), '', 'array');
		$item_id = $cid['0'];
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=survey.edit&id=' . $item_id, false));
	}

	public function preview() {
		$database = JFactory::getDbo();
		$cid      = (int) end(JFactory::getApplication()->input->get('cid', array (), 'array'));

		$unique_id = md5(uniqid(rand(), true));
		$query     = "INSERT INTO `#__survey_force_previews` SET `preview_id` = '" . $unique_id . "', `time` = '" . strtotime(JFactory::getDate()) . "'";
		$database->setQuery($query);
		$database->query();

		$this->setRedirect(JRoute::_(JUri::root() . "index.php?option=com_surveyforce&view=survey&id={$cid}&preview=" . $unique_id));
	}


	// 以下都是議題預覽功能
	public function preview_intro() {

		$config        = JFactory::getConfig();
		$expire_minute = $config->get('expire_minute', 30);

		SurveyforceHelper::setPreviewData($this->id, "expire_time", time() + ($expire_minute * 60), true);

		// 若為不驗證(圖形驗證)，且沒有提供抽獎，則略過個資頁
		if ($this->survs->verify_type == '["none"]' && $this->survs->is_lottery == 0) {

			// 若為擇一且有多個驗證方式，則轉向多步驟頁面
			if ($this->survs->verify_required == 0 && count(json_decode($this->survs->verify_type)) > 1) {
				$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}&next=verify_opt", false);
			} else {
				$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}&next=verify", false);
			}
		} else {
			$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}&next=statement", false);
		}

		$this->setRedirect($link);

	}

	public function preview_statement() {

		if (!SurveyforceHelper::isSurveyExpired($this->id)) {
			$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}", false);
			$this->setRedirect($link);
		}

		$app    = JFactory::getApplication();
		$action = $app->input->getString("action");

		switch ($action) {
			case "previously_step":  // 上一頁
				$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&id={$this->id}&tmpl=component", false);
				break;
			case "next_step": // 下一頁

				// 若為擇一且有多個驗證方式，則轉向多步驟頁面
				if ($this->survs->verify_required == 0 && count(json_decode($this->survs->verify_type)) > 1) {
					$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}&next=verify_opt", false);
				} else {
					$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}&next=verify", false);
				}

				break;
		}


		$this->setRedirect($link);

	}

	public function preview_verify_opt() {

		if (!SurveyforceHelper::isSurveyExpired($this->id)) {
			$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}", false);
			$this->setRedirect($link);
		}

		$app    = JFactory::getApplication();
		$action = $app->input->getString("action");

		switch ($action) {
			case "previously_step": // 上一頁
				if ($this->survs->verify_type == '["none"]' && $this->survs->is_lottery == 0) {
					$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&id={$this->id}&tmpl=component", false);
				} else {
					$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&id={$this->id}&tmpl=component&next=statement", false);
				}
				break;
			case "next_step": // 下一頁
				$type   = $app->input->getString("type");
				SurveyforceHelper::setPreviewData($this->id, "preview_type", $type);
				// 進入驗證資料頁
				$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}&next=verify", false);

				break;
		}

		$this->setRedirect($link);

	}

	public function preview_verify() {

		if (!SurveyforceHelper::isSurveyExpired($this->id)) {
			$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}", false);
			$this->setRedirect($link);
		}

		$app    = JFactory::getApplication();
		$action = $app->input->getString("action");

		switch ($action) {
			case "previously_step":  // 上一頁

				if ($this->survs->verify_required == 0 && count(json_decode($this->survs->verify_type)) > 1) { // 若為擇一且有多個驗證方式
					$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&id={$this->id}&tmpl=component&next=verify_opt", false);
				} else {
					if ($this->survs->verify_type == '["none"]' && $this->survs->is_lottery == 0) {
						$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&id={$this->id}&tmpl=component", false);
					} else {
						$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&id={$this->id}&tmpl=component&next=statement", false);
					}
				}

				break;
			case "next_step": // 下一頁

				if (!SurveyforceHelper::getPreviewData($this->id, "preview_type")) {
					$app  = JFactory::getApplication();
					$type = $app->input->getString("type");
					SurveyforceHelper::setPreviewData($this->id, "preview_type", $type);
				} else {
					$type = SurveyforceHelper::getPreviewData($this->id, "preview_type");
				}

				if ($type == "none") {
					$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}&next=question", false);
				} else {
					JPluginHelper::importPlugin('verify', $type);
					$className = 'plgVerify' . ucfirst($type);
					if ($className::onGetFormHtml2nd()) {
						$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}&next=verify2nd", false);
					} else {
						$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}&next=question", false);
					}
				}

				break;

		}

		$this->setRedirect($link);

	}

	public function preview_verify2nd() {

		if (!SurveyforceHelper::isSurveyExpired($this->id)) {
			$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}", false);
			$this->setRedirect($link);
		}

		$app    = JFactory::getApplication();
		$action = $app->input->getString("action");

		switch ($action) {
			case "previously_step":  // 上一頁
				$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&id={$this->id}&tmpl=component&next=verify", false);
				break;
			case "next_step":
				$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}&next=question", false);
				break;
		}

		$this->setRedirect($link);

	}

	public function preview_question() {

		if (!SurveyforceHelper::isSurveyExpired($this->id)) {
			$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}", false);
			$this->setRedirect($link);
		}

		$app    = JFactory::getApplication();
		$action = $app->input->getString("action");

		switch ($action) {
			case "previously_step":  // 上一頁
				$type = SurveyforceHelper::getPreviewData($this->id, "preview_type");
				if ($type == "none") {
					$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}&next=verify", false);
				} else {
					JPluginHelper::importPlugin('verify', $type);
					$className = 'plgVerify' . ucfirst($type);
					if ($className::onGetFormHtml2nd()) {
						$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}&next=verify2nd", false);
					} else {
						$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}&next=verify", false);
					}
				}
				break;

			case "next_step":        // 下一頁
				$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&id={$this->id}&tmpl=component&next=finish", false);

				break;

			case "next_question": // 下一題

				$question_id = $app->input->getInt("qid");

				unset($option_answers);
				$option_answers = SurveyforceHelper::getPreviewData($this->id, "option_answers");
				if ($option_answers == "") { // 找無資料，表示尚未記錄過
					$option_answers = array ();
				}
				$option_answers[$question_id] = 1;
				SurveyforceHelper::setPreviewData($this->id, "option_answers", $option_answers);

				// 檢查所有題目是否都已做過，若尚未，則轉入該題目。
				require_once(JPATH_SITE . "/components/com_surveyforce/models/question.php");
				$questions = SurveyforceModelQuestion::getQuestions($this->id);
				foreach ($questions as $question) {
					if (!array_key_exists($question->id, $option_answers)) {
						$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&id={$this->id}&qid={$question->id}&next=question&tmpl=component", false);
						$this->setRedirect($link);

						return;
					}
				}

				break;

			case "previously_question": // 上一題

				$question_id = $app->input->getInt("qid");

				require_once(JPATH_SITE . "/components/com_surveyforce/models/question.php");
				$questions = SurveyforceModelQuestion::getQuestions($this->id);
				foreach ($questions as $key => $question) {
					if ($question->id == $question_id) {
						$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&id={$this->id}&qid={$questions[$key - 1]->id}&next=question&tmpl=component", false);
						$this->setRedirect($link);

						return;
					}
				}


				break;
		}

		$this->setRedirect($link);

	}

	public function preview_finish() {

		if (!SurveyforceHelper::isSurveyExpired($this->id)) {
			$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&tmpl=component&id={$this->id}", false);
			$this->setRedirect($link);
		}

		$app    = JFactory::getApplication();
		$action = $app->input->getString("action");

		switch ($action) {
			case "previously_step":
				if (SurveyforceHelper::getPreviewData($this->id, "success") == true) {
					SurveyforceHelper::setPreviewData($this->id, "success", false);
					$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&id={$this->id}&next=finish&tmpl=component", false);
				} else {
					if (SurveyforceHelper::getPreviewData($this->id, "lottery") == true) {
						SurveyforceHelper::setPreviewData($this->id, "lottery", false);
						$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&id={$this->id}&next=finish&tmpl=component", false);
					} else {
						require_once(JPATH_SITE . "/components/com_surveyforce/models/question.php");
						$questions   = SurveyforceModelQuestion::getQuestions($this->id);
						$question_id = end($questions)->id;
						$link        = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&id={$this->id}&qid={$question_id}&next=question&tmpl=component", false);
					}
				}

				break;

			case "next_step":
				if ($this->survs->is_lottery) {
					if (SurveyforceHelper::getPreviewData($this->id, "lottery")) {
						SurveyforceHelper::setPreviewData($this->id, "success", true);
					} else {
						SurveyforceHelper::setPreviewData($this->id, "lottery", true);
					}
				} else {
					SurveyforceHelper::setPreviewData($this->id, "success", true);
				}

				$link = JRoute::_("index.php?option=com_surveyforce&view=surveys&layout=preview&id={$this->id}&next=finish&tmpl=component", false);

				break;
		}

		$this->setRedirect($link);

	}

	public function other_data() {

		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app      = JFactory::getApplication();
		$config   = JFactory::getConfig();

		$original_name = $app->input->getString('original_name');
		$survey_id = $app->input->getInt('survey_id');
		$file_name = $app->input->getString('file_name');
		$path     = JPATH_SITE . '/' . $config->get('ivoting_path') . '/survey/pdf/' . $survey_id . '/' . $file_name . '.pdf';

		header('Cache-Control: public, must-revalidate');
		header('Content-Type: application/octet-stream');
		header('Content-Length: ' . (string) (filesize($path)));
		header('Content-Disposition: attachment; filename="' . $original_name . '"');
		readfile($path);

		exit;
	}

}
