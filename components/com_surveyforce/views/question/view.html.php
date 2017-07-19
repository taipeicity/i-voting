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
class SurveyforceViewQuestion extends JViewLegacy {

    public function __construct() {
        parent::__construct();
    }

    public function display($tpl = null) {
		$model = $this->getModel();
		$session 	= &JFactory::getSession();

		$config = JFactory::getConfig();
		$app = JFactory::getApplication();
		$this->itemid = $app->input->getInt('Itemid');
		$this->survey_id = $app->input->getInt('sid');
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
		$category_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$this->itemid}", false);
		$intro_link = JRoute::_("index.php?option=com_surveyforce&view=intro&sid={$this->survey_id}&Itemid={$this->itemid}", false);

		// 檢查是否閒置過久
		if (SurveyforceVote::isSurveyExpired($this->survey_id) == false) {
			$msg = "網頁已閒置過久，請重新點選議題進行投票。";
			$app->redirect($category_link, $msg);
		}

		// 檢查議題是否有效
		if (SurveyforceVote::isSurveyValid($this->survey_id) == false) {
			$msg = "該議題目前未在可投票時間內，請重新選擇。";
			$app->redirect($category_link, $msg);
		}


		// 檢查是否有依序執行步驟
		if (SurveyforceVote::checkSurveyStep($this->survey_id, "question") == false) {
			$msg = "該議題未從投票起始頁進入，請重新執行。";
			$app->redirect($intro_link, $msg);
		}


		// 取得所有題目
		$this->questions = $model->getQuestions($this->survey_id);
		$this->questions_num = count($this->questions);
		if ($this->questions_num == 0) {
			$msg = "該議題並無任何題目，請重新選擇其他議題進行投票。";

			$app->redirect($category_link, $msg);
		}

		// 若題目ID為0 ，則給第1個題目ID
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
			$msg = "題目編號錯誤，請重新選擇其他議題進行投票。";

			$app->redirect($category_link, $msg);
		}

		// 檢查題目是否屬於該議題
		if ($this->question->sf_survey != $this->survey_id ) {
			$msg = "該題目並非屬於該議題之一，請重新選擇。";

			$app->redirect($category_link, $msg);
		}

		// 取得選項
		$this->options = $model->getOptions($this->question_id);
		if (count($this->options) == 0) {
			$msg = "該題目並無任何選項，請重新選擇其他議題進行投票。";

			$app->redirect($category_link, $msg);
		}


		// 取得子選項
		$this->sub_options = $model->getSubOptions($this->question_id);

		$this->intro_link = $intro_link;
		$this->category_link = $category_link;

		$document = JFactory::getDocument();
		$document->setTitle($this->escape($this->survey->title));

		// 判斷瀏覽器版本，是否為IE7 或 8
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if ( preg_match('/MSIE 8.0/i', $u_agent) || preg_match('/MSIE 7.0/i', $u_agent)) {
			$this->other_snapshot = true;
		}
		$this->other_snapshot = true;	// 先關閉快照

		 // Display the view
		$layout	= $app->input->getString('layout', 'default');
		$this->setLayout($layout);

		// 有分類的題目
		if ($this->question->question_type == "imgcat") {
			$this->cats = $model->getQuestionCats($this->question_id);
			$this->setLayout("imgcat");
		}


        parent::display($tpl);
        
    }

}
