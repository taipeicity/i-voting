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
class SurveyforceViewSurveys_traffic extends JViewLegacy {

	protected $items;
	protected $state;


	public function display($tpl = null) {
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->model = $this->getModel();
		
		$app = JFactory::getApplication();
		
		// 接收篩選表單的資料
		$this->is_search = $app->input->getInt('is_search');
		$this->filter_survey_id = $app->input->getInt('filter_survey_id');
		$this->filter_date = $app->input->getString('filter_date', date("Y-m-d"));
				
		// 是否有篩選
		if ($this->is_search) {
			// 取得所有票號
			$this->tickets = $this->model->getVoteTickets($this->filter_survey_id, $this->filter_date);
			
			// 整理
			$this->total_vote = 0;	// 總人數
			
			// 每小時的人數
			$this->hour_traffic = [];
			for ($h = 1; $h <= 24; $h++) {
				$this->hour_traffic[$h] = 0;
			}
		
			if ($this->tickets) {
				foreach ($this->tickets as $ticket) {
					$hour = date("G", strtotime(JHtml::_('date', $ticket->created, "Y-m-d H:i:s"))) +1;		// 取出時間並轉換成小時
					$this->hour_traffic[$hour] += 1;
					
					$this->total_vote += 1;
				}
			}
			
			// 要寫入Char的數據
			$this->vote_traffic_data = [];
			for ($h = 1; $h <= 24; $h++) {
				$this->vote_traffic_data[] = sprintf("[\"%02d:00\", %0d]", $h, $this->hour_traffic[$h]);
			}
		}

		// 取得所有議題
		$this->surveys = $this->model->getSurveys();


		JToolBarHelper::title("議題流量分析");


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
