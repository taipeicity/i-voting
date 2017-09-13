<?php

/**
*   @package         Surveyforce
*   @version           1.1-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * Question Controller
 */
class SurveyforceControllerQuestion extends JControllerForm {

    public function __construct($config = array()) {
		if ( JFactory::getApplication()->input->get('surv_id', 0) )
			JFactory::getApplication()->setUserState('question.sf_survey', JFactory::getApplication()->input->get('surv_id', 0));

        parent::__construct($config);
    }

    public function new_question_type() {

		if ( JFactory::getApplication()->input->get('surv_id', 0) )
			JFactory::getApplication()->setUserState('question.sf_survey', JFactory::getApplication()->input->get('surv_id', 0));

        require_once(JPATH_BASE . '/components/com_surveyforce/views/newquestion/view.html.php');
        $view = $this->getView("newquestion");
        $view->display();
    }

 
    
    public function orderup() {

        $db = JFactory::getDbo();
		$id = JFactory::getApplication()->input->get('cid', array(), 'ARRAY');
        $id = $id[0];

        $query = 'SELECT ordering FROM `#__survey_force_quests` WHERE id=' . $id;
        $db->setQuery($query);
        $order = $db->loadRow();
        
       
        $query = 'UPDATE `#__survey_force_quests` SET `ordering` =' . (intval($order[0]) - 1) . ' WHERE id=' . $id;
        $db->setQuery($query);
        $db->execute();
		JFactory::getApplication()->redirect('index.php?option=com_surveyforce&view=questions&surv_id='.JFactory::getApplication()->input->get('surv_id', 0));
    }

    public function orderdown() {

        $db = JFactory::getDbo();
        $id = JFactory::getApplication()->input->get('cid', array(), 'array');
        $id = $id[0];
        
        $query = 'SELECT ordering FROM `#__survey_force_quests` WHERE `id`=' . $id;
        $db->setQuery($query);
        $order = $db->loadRow();

        if (intval($order) > 0) {
            $query = 'UPDATE `#__survey_force_quests` SET `ordering`=' . (intval($order[0]) + 1) . ' WHERE id=' . $id;
            $db->setQuery($query);
            $db->execute();
        }
		JFactory::getApplication()->redirect('index.php?option=com_surveyforce&view=questions&surv_id='.JFactory::getApplication()->input->get('surv_id', 0));
    }

   

	public function cancel()
	{
		if (JFactory::getApplication()->getUserState( "question.sf_survey"))
			JFactory::getApplication()->redirect('index.php?option=com_surveyforce&view=questions&surv_id='.JFactory::getApplication()->getUserState( "question.sf_survey"));
		else
			parent::cancel();
	}

	public function save()
	{
		$res = parent::save();

		if ( JFactory::getApplication()->input->get('task') == 'save')
			if ( $res && JFactory::getApplication()->getUserState( "question.sf_survey") )
				JFactory::getApplication()->redirect('index.php?option=com_surveyforce&view=questions&surv_id='.JFactory::getApplication()->getUserState( "question.sf_survey"));

		return $res;
	}

	


	protected function postSaveHook(JModelLegacy $model, $validData = array()) {
		
	}

}
