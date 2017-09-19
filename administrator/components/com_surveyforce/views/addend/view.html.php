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
class SurveyforceViewAddend extends JViewLegacy {

	protected $items;
    protected $state;
    

    public function display($tpl = null) {
        $app = JFactory::getApplication();
		$this->surv_id = JFactory::getApplication()->input->get('surv_id', 0);

		$this->survey_item = SurveyforceHelper::getSuveryItem($this->surv_id);
		JToolBarHelper::title("投票管理:". $this->survey_item->title. " - 補送選票");

        $this->state = $this->get('State');

		$this->verify_params = json_decode( $this->survey_item->verify_params );
		
		if ($this->verify_params->idnum->idnum_table_suffix == "") {
			JError::raiseWarning( 100, '無資料表後綴字，請重新檢查驗證方式。' );
            return false;
		}

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
