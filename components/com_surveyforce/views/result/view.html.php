<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
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

		$app = JFactory::getApplication();
		$session = &JFactory::getSession();
		$this->itemid = $app->input->getInt('Itemid');
		$this->survey_id = $app->input->getInt('sid');

		$this->state = $this->get('state');
		$this->params = $this->state->get('params');

		$this->item = $this->get('Item');

		$this->orderby = $app->input->getInt('orderby');  // 排序方式
		if (!isset($this->orderby)) {
			$this->orderby = 0;
		}

		$this->chart = $app->input->getVar('chart');  // 圖形呈現方式
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
			$msg = "議題資料不存在，請重新選擇。";
			$app->redirect($category_link, $msg);
		}


		// 是否顯示結果
		switch ($this->item->display_result) {
			case 0:  // 不顯示
				$msg = "本議題不提供投票結果顯示";
				break;
			case 1:  // 投票中顯示
				break;
			case 2:  // 議題結束後才顯示
				$date = JFactory::getDate();
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
					$qtype = array ("select", "number", "table");
					//練習模式取得題目及選項
					require_once(JPATH_COMPONENT . '/models/question.php');
					$Questions = SurveyforceModelQuestion::getQuestions($this->survey_id);
					foreach ($Questions as $num => $qid) {
						if (!array_key_exists($qid->id, $option_answers)) {
							$next_question_link = JRoute::_("index.php?option=com_surveyforce&view=question&sid={$this->survey_id}&qid={$qid->id}&Itemid={$this->itemid}", false);
							$app->redirect($next_question_link);
							return;
						}
						$Question = SurveyforceModelQuestion::getQuestion($qid->id); //題目名稱：$Question->sf_qtext
						$Options = SurveyforceModelQuestion::getOptions($qid->id); //選項名稱：$Options->ftext 

						$options[$Question->id] = new stdClass(); //仿照正式區的資料格式存入資料
						$options[$Question->id]->quest_title = $Question->sf_qtext;
						$options[$Question->id]->quest_type = $Question->question_type;
						foreach ($Options as $Option) {
							$options[$Question->id]->field_title[$Option->id] = $Option->ftext;
						}
						//依據不同題目類型寫入題目及選項資料
						if (in_array($Question->question_type, $qtype)) {  //有子選項
							$SubOptions = SurveyforceModelQuestion::getSubOptions($qid->id); //子選項名稱：$SubOption->title
							foreach ($Options as $serial => $Option) {
								foreach ($SubOptions as $SubOption) {
									$join = $option_answers[$qid->id][$serial]["field_id"] . "_" . $option_answers[$qid->id][$serial]["sub_field_id"];
									$OSO = $Option->id . "_" . $SubOption->id;
									if ($OSO == $join) {
										$sub_options[$join] = new stdClass();
										$sub_options[$join]->field_id = $Option->id;
										$sub_options[$join]->sub_field_id = $SubOption->id;
										$sub_options[$join]->sub_field_title = $SubOption->title;
										$sub_options[$join]->count = "1";
									}
								}
							}
						} else { //無子選項						
							foreach ($options[$Question->id]->field_title as $field_id => $field_title) {
								foreach ($option_answers[$Question->id] as $field) {
									if ($field["field_id"] == $field_id) {
										$options[$Question->id]->count[$field_id] = "1";
									}
								}
							}
						}
					}
					$this->results = $options;
					$this->sub_results = $sub_options;
					$this->sub_fields = $model->getSubFields($this->orderby);
				} else {
					$msg = '該議題未從投票啟始頁進入，請重新執行。';
					$app->redirect($category_link, $msg);
				}
			} else {  //正式區
				$this->fields = $model->getFields($this->orderby); //選項
				$this->sub_fields = $model->getSubFields($this->orderby); //子選項
				$this->results = $this->get('Results'); //網路
				$this->sub_results = $this->get('SubResults'); //網路子選項
				$this->paper = $this->get('PaperResults'); //紙本
				$this->sub_paper = $this->get('PaperSubResults'); //紙本紙選項
				$this->place = $this->get('PlaceResults'); //現地
				$this->sub_place = $this->get('PlaceSubResults'); //現地子選項

				$this->update_time = $this->get('ResultsTime'); //更新時間
			}
		}

		$document = JFactory::getDocument();
		$document->setTitle($this->escape($this->item->title));

		// Display the view
		parent::display($tpl);

	}

}
