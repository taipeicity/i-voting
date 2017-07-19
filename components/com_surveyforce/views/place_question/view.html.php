<?php

/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewPlace_question extends JViewLegacy {

    public function __construct() {
        parent::__construct();
    }

    public function display($tpl = null) {
		$model = $this->getModel();
		$session 	= &JFactory::getSession();
		$app = JFactory::getApplication();
		
		$this->itemid	= $app->input->getInt('Itemid');
		$this->survey_id	= $app->input->getInt('sid');
		$this->question_id	= $app->input->getInt('qid', 0);
        

        
        $this->state = $this->get('state');
        $this->params = $this->state->get('params');

        $this->survey = $this->get('Survey');

       

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

		// 檢查
		$login_link = JRoute::_("index.php?option=com_surveyforce&view=place_login&Itemid={$this->itemid}", false);
		$category_link = JRoute::_("index.php?option=com_surveyforce&view=place_category&Itemid={$this->itemid}", false);
		$verify_link = JRoute::_("index.php?option=com_surveyforce&view=place_verify&sid={$this->survey_id}&Itemid={$this->itemid}", false);

		
		// 檢查是否有登入
		if (!$session->get('place_username')) {
			$msg = "您尚未登入，請重新登入。";
			$app->redirect($login_link, $msg);
		}

		// 檢查議題是否有效
		if (SurveyforceVote::isSurveyValid($this->survey_id) == false) {
			$msg = "該議題已結束投票時間。";
			$app->redirect($category_link, $msg);
		}

		// 檢查是否已驗證通過
		if ($session->get('place_verify') == false || $session->get('place_verify_idnum') == false) {
			$msg = "您尚未通過身分證驗證，請重新驗證。";
			$app->redirect($verify_link, $msg);
		}

		// 取得所有題目
		$this->questions = $model->getQuestions($this->survey_id);
		$this->questions_num = count($this->questions);
		if ($this->questions_num == 0) {
			$msg = "該議題並無任何題目，請重新選擇其他議題進行投票。";
			$app->redirect($category_link, $msg);
		}
		$this->question_id = ($this->question_id == 0) ? $this->questions[0]->id : $this->question_id;

		// 計算題目編號
		$this->count = 1;
		foreach ($this->questions as $question) {
			if ($question->id == $this->question_id) {
				break;
			}
			$this->count++;
		}


		// 取得目前的題目
		$this->question = $model->getQuestion($this->question_id);
		if (!is_object($this->question)) {
			return JError::raiseError(404, "題目編號錯誤，請重新選擇其他議題進行投票。");
		}

		// 取得選項
		$this->options = $model->getOptions($this->question_id);
		if (count($this->options) == 0) {
			$msg = "該題目並無任何選項，請重新選擇其他議題進行投票。";
			$app->redirect($category_link, $msg);
		}

		// 取得子選項
		$this->sub_options = $model->getSubOptions($this->question_id);


		$document = JFactory::getDocument();
		$document->setTitle($this->escape($this->survey->title));


		// 有分類的題目
		if ($this->question->question_type == "imgcat") {
			$this->cats = $model->getQuestionCats($this->question_id);
			$this->setLayout("imgcat");
		}



        parent::display($tpl);
        
    }

}
