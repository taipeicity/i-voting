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
class SurveyforceViewPrint extends JViewLegacy {

	protected $items;
	protected $state;


	public function display($tpl = null) {
		$this->model   = $this->getModel();
		$config        = JFactory::getConfig();
		$app           = JFactory::getApplication();
		$this->surv_id = JFactory::getApplication()->input->get('surv_id', 0);

		$this->survey_item = SurveyforceHelper::getSuveryItem($this->surv_id);
		$desc              = $this->survey_item->desc;
		// 處理議題列印的檔案路徑
		preg_match_all('/href\=\"(.+)\"\>\</', $this->survey_item->desc, $match_href);
		foreach ($match_href[1] as $key => $path) {
			$this->survey_item->desc = str_replace($path, JURI::root() . $path, $this->survey_item->desc);
		}
		// 處理議題列印的檔案路徑
		preg_match_all('/src\=\"(.+)\" alt/', $this->survey_item->desc, $match_img);
		foreach ($match_img[1] as $key => $path) {
			$this->survey_item->desc = str_replace($path, JURI::root() . $path, $this->survey_item->desc);
		}

		$this->state = $this->get('State');

		if (!$this->survey_item->id) {
			echo "議題錯誤";
			jexit();
		}

		if ($this->survey_item->other_data) {
			$other_data[] = $this->survey_item->other_data;
		}

		if ($this->survey_item->other_data2) {
			$other_data[] = $this->survey_item->other_data2;
		}

		if ($this->survey_item->other_data3) {
			$other_data[] = $this->survey_item->other_data3;
		}
		$this->other_data = $other_data;


		$this->questions = $this->model->getQuestions($this->surv_id);


		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}


		parent::display($tpl);
		jexit();
	}


}
