<?php

/**
 * @package            Surveyforce
 * @version            1.2-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewSurveys_count extends JViewLegacy {

	protected $items;
	protected $state;


	public function display($tpl = null) {
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->model = $this->getModel();
		
		$app = JFactory::getApplication();
		
		// 接收篩選表單的資料
		$this->is_search = $app->input->getInt('is_search');
		$this->filter_year = $app->input->getInt('filter_year', date("Y"));
		$this->filter_survey_ids = $app->input->getInt('filter_survey_ids');
		
		// 是否有篩選
		if ($this->is_search) {
			// 總議題數
			$this->total_survey = $this->model->getVoteTotalSurvey( $this->filter_year, $this->filter_survey_ids);
			
			// 總投票人數
			$this->total_vote = $this->model->getVoteTotalTicket( $this->filter_year, $this->filter_survey_ids);
			
			// 取得所有驗證方式
			$this->verify_types = $this->model->getAllVerifyTypes();
			$this->verify = $this->model->getVerifyCount("", $this->filter_year, $this->filter_survey_ids);
			$this->api_verify = $this->model->getVerifyCount("taipeicard", $this->filter_year, $this->filter_survey_ids);
			
			// 性別
			$this->male = $this->model->getSexCount(1, "", $this->filter_year, $this->filter_survey_ids);
			$this->female = $this->model->getSexCount(2, "", $this->filter_year, $this->filter_survey_ids);
			$this->api_male = $this->model->getSexCount(1, "taipeicard", $this->filter_year, $this->filter_survey_ids);
			$this->api_female = $this->model->getSexCount(2, "taipeicard", $this->filter_year, $this->filter_survey_ids);
			
			// 年齡
			$this->age = $this->model->getAgeCount("", $this->filter_year, $this->filter_survey_ids);
			$this->api_age = $this->model->getAgeCount("taipeicard", $this->filter_year, $this->filter_survey_ids);
			
			
		}

		// 搜尋年份
		$this->years = [];
		$this->year_surveys = [];
		for ($y = 2016; $y <= date("Y"); $y++) {
			$this->years[$y] = $y. "年";
			$this->year_surveys[$y] = [];
		}
		
		// 議題
		$this->surveys = $this->model->getSurveys();
		// 依投票時間做年份整理
		for ($y = 2016; $y <= date("Y"); $y++) {
			foreach ($this->surveys as $survey) {
				if (date("Y", strtotime($survey->vote_start)) <= $y && date("Y", strtotime($survey->vote_end)) >= $y) {
					$this->year_surveys[$y][] = array("id" => $survey->id, "title" => sprintf("%d - %s", $survey->id, $survey->title));
				}
			}
		}
		
		
		// 年齡標籤
		$this->age_label = array(1 => "16~19", 2 => "20~29", 3 => "30~39", 4 => "40~49", 5 => "50~59", 6 => "60~69", 7 => "70~79", 8 => "80~89", 9 => "90以上");
		
		

		JToolBarHelper::title("議題數據分析");


		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JFactory::getApplication()->enqueueMessage(implode('<br />', $errors), 'error');

			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		JFactory::getApplication()->input->set('hidemainmenu', true);

		JToolBarHelper::cancel('survey.cancel', 'JTOOLBAR_CLOSE');

	}

}
