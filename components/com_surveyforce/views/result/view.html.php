<?php

/**
 * @package            Surveyforce
 * @version            1.3-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewResult extends JViewLegacy {

	public function __construct() {
		parent::__construct();

	}

	public function display($tpl = null) {

		$app             = JFactory::getApplication();
		$session         = &JFactory::getSession();
		$this->itemid    = $app->input->getInt('Itemid');
		$this->survey_id = $app->input->getInt('sid');

		$this->state  = $this->get('state');
		$this->params = $this->state->get('params');

		$this->item = $this->get('Item');

		if ($session->get('practice_pattern')) {
			$pattern = "practice";
		} else {
			$pattern = "formal";
		}
		$this->orderby = $session->get($pattern . '_orderby'); // 排序方式
		if (!isset($this->orderby)) {
			$this->orderby = 1;
		}

		$this->chart = $session->get($pattern . '_chart');  // 圖形呈現方式
		if (!isset($this->chart)) {
			$this->chart = "bar";
		}


		$model = $this->getModel();

		$this->device = JHtml::_('utility.getDeviceCode');


		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// 檢查
		$category_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$this->itemid}", false);

		// 檢查議題是否有效
		if ($this->item->id == 0) {
			$msg = "該議題不存在，請重新選擇正確的議題。";
			$app->redirect($category_link, $msg);
		}

		if ($this->item->is_public == 0) {
			$check = 0;
			if ($app->input->getString('token') != JHtml::_('utility.endcode', $this->item->title)) { // 檢查token
				$check++;
			}
			if (SurveyforceVote::checkSurveyStep($this->survey_id, "token") == false) {
				$check++;
			}
			if ($check > 1) {
				$msg = "該議題不存在，請重新選擇正確的議題。";
				$app->redirect($category_link, $msg);
			}
		}

		// 是否顯示結果
		switch ($this->item->display_result) {
			case 0:  // 不顯示
				$msg = "本議題不提供投票結果顯示";
				break;
			case 1:  // 投票中顯示
				break;
			case 2:  // 議題結束後才顯示
				$date    = JFactory::getDate();
				$nowDate = $date->toSql();
				if ($this->item->vote_end > $nowDate) {  // 檢查是議題是否進行中
					$msg = "本議題於投票結束後才顯示投票結果";
				}

				break;
		}


		if ($msg) {
			$app->redirect($category_link, $msg);
		} else {
			if ($session->get('practice_pattern')) {  //練習區
				//取得選項紀錄
				$option_answers = SurveyforceVote::getSurveyData($this->survey_id, "option_answers");
				if ($option_answers) {

					$items = $model->getQuestField($option_answers);

					$this->results     = $items['options'];
					$this->sub_results = $items['sub_options'];
					$this->sub_fields  = $model->getSubFields($this->orderby);
				} else {
					$msg = '該議題未從投票啟始頁進入，請重新執行。';
					$app->redirect($category_link, $msg);
				}
			} else {  //正式區
				$this->fields      = $model->getFields($this->orderby); //選項
				$this->sub_fields  = $model->getSubFields($this->orderby); //子選項
				$this->results     = $this->get('Results'); //網路
				$this->sub_results = $this->get('SubResults'); //網路子選項
				$this->paper       = $this->get('PaperResults'); //紙本
				$this->sub_paper   = $this->get('PaperSubResults'); //紙本紙選項
				$this->place       = $this->get('PlaceResults'); //現地
				$this->sub_place   = $this->get('PlaceSubResults'); //現地子選項
				$this->rank        = $this->get('Rank'); //名次變動
				$this->rank_sub    = $this->get('RankSub'); //子選項名次變動

				$this->update_time = $this->get('ResultsTime'); //更新時間
			}
		}

		$document = JFactory::getDocument();
		$document->setTitle($this->escape($this->item->title));

		// Display the view
		parent::display($tpl);

	}

}
