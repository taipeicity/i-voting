<?php

/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class SurveyforceViewAnalyze extends JViewLegacy
{
    protected $form;

    protected $table;

    protected $item;

    protected $fields;

    protected $layout;

    public function display($tpl = null)
    {

        $app = JFactory::getApplication();
        $this->layout = $app->input->getString("layout");
        $this->surv_id = $app->input->getInt("surv_id");
		$this->survey_item = SurveyforceHelper::getSuveryItem($this->surv_id);

        if ($this->getLayout() == 'edit') {
            SurveyforceHelper::showTitle('analyzes');

            $this->state = $this->get('State');
            $this->form = $this->get('Form');
            $this->table = $this->get('Table');
            $this->item = $this->get('Item');
            $this->fields = $this->get('Fields');
        } else {
            SurveyforceHelper::showTitle('analyzes_result');
			
			// 性別分析
            $this->male = $this->get('MaleCount');
            $this->female = $this->get('FemaleCount');
            $this->totalsex = $this->male + $this->female;

			// 年齡分析
            $ageCount = $this->get('AgeCount');
            $this->age = $this->getAgeCount($ageCount);
			
			
			// API性別分析
            $this->api_male = $this->get('ApiMaleCount');
            $this->api_female = $this->get('ApiFemaleCount');
            $this->api_totalsex = $this->api_male + $this->api_female;

			// API年齡分析
            $api_ageCount = $this->get('ApiAgeCount');
            $this->api_age = $this->getAgeCount($api_ageCount);
			
			

            $this->result = $this->get('Result');

        }

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode('<br />', $errors), 'error');

            return false;
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        if ($this->layout == 'edit') {
            JToolBarHelper::apply('analyze.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('analyze.save', 'JTOOLBAR_SAVE');
            if ($isNew) {
                JToolBarHelper::cancel('analyze.cancel', 'JTOOLBAR_CANCEL');
            } else {
                JToolBarHelper::cancel('analyze.cancel', 'JTOOLBAR_CLOSE');
            }
        } else {
            JToolBarHelper::cancel('survey.cancel', 'JTOOLBAR_CLOSE');
        }
    }

    public function getAgeCount($ageCount)
    {
        $Range = [
            1 => ['title' => '16~19'],
            2 => ['title' => '20~29'],
            3 => ['title' => '30~39'],
            4 => ['title' => '40~49'],
            5 => ['title' => '50~59'],
            6 => ['title' => '60~69'],
            7 => ['title' => '70~79'],
            8 => ['title' => '80~89'],
            9 => ['title' => '90以上'],
        ];

        $totalAgeCount = 0;
        foreach ($Range as $key => $item) {
			if ($key == 9) {	// 超過90歲
				$Range[$key]['count'] = isset($ageCount[9]) ? (int) $ageCount[9]->count : 0;
				$Range[$key]['count'] += isset($ageCount[0]) ? (int) $ageCount[0]->count : 0;
				
				if (isset($ageCount[9])) {
					$totalAgeCount += (int) $ageCount[9]->count;
				}
			
				if (isset($ageCount[0])) {
					$totalAgeCount += (int) $ageCount[0]->count;
				}
			} else {
				$Range[$key]['count'] = isset($ageCount[$key]) ? (int) $ageCount[$key]->count : 0;
				
				if (isset($ageCount[$key])) {
					$totalAgeCount += (int) $ageCount[$key]->count;
				}
			}
			
            
        }

        return ['age' => $Range, 'total' => $totalAgeCount];
    }

   
}
