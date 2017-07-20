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
class SurveyforceViewResult extends JViewLegacy {

	protected $items;
    protected $state;
    

    public function display($tpl = null) {

        $app = JFactory::getApplication();


        $this->state = $this->get('State');
		$this->item = $this->get('Item');

		$this->total_voters = $this->get('TotalVoters');	// 取得總投票人數

		if ($this->total_voters) {
			// 類型、題目、選項題目資料
			$this->questions	= $this->get('Questions');
			$this->sub_questions = $this->get('SubQuestions');


			$this->fields 		= $this->get('Fields');
			$this->sub_fields 	= $this->get('SubFields');
			$this->results 		= $this->get('Results');
			$this->sub_results 	= $this->get('SubResults');
			$this->paper 		= $this->get('PaperResults');
			$this->sub_paper 	= $this->get('PaperSubResults');
			$this->place 		= $this->get('PlaceResults');
			$this->sub_place 	= $this->get('PlaceSubResults');


			// 取得開放式欄位資料
			$this->open			= $this->get('OpenResults');

		}

		

		JToolBarHelper::title("投票管理:". $this->item->title. " - 觀看結果");
		
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
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
