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
 * HTML View class for the SurveyForce Deluxe Component
 */
class SurveyforceViewNewquestion extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	
    public function display($tpl = null) 
    {		
        
//		$this->questions	= $this->getAllquestions();
		$this->question_type	= $this->getAllQuestionType();
                
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
				
		parent::display($tpl);
    }
	
	public function getAllquestions(){
		$db = JFactory::getDBO();
		
		$db->setQuery("SELECT * FROM `#__survey_force_qtypes`");
		$questions = $db->loadObjectList();
		
		return $questions;
	 }

	 public function getAllQuestionType(){
		$db = JFactory::getDBO();

		$db->setQuery("SELECT * FROM `#__extensions` WHERE `type` = 'plugin' and `access` = '1' and `enabled` = '1' and `folder` = 'survey' order by ordering ASC");
		$questions = $db->loadObjectList();

		return $questions;
	 }


}
?>
