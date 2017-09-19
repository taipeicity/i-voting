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
class SurveyforceViewGetip extends JViewLegacy {

    protected $items;
    protected $state;

    public function display($tpl = null) {

        $app = JFactory::getApplication();
        $layout = $app->input->getString("layout");

//        $this->state = $this->get('State');
        $this->item = $this->get('Item');

        JToolBarHelper::title("投票管理: 匯出投票來源");

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
