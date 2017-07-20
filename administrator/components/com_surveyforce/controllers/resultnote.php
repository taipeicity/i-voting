<?php
/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die('Restricted access');

class SurveyforceControllerResultnote extends JControllerForm {

	protected $last_insert_id;

	public function __construct() {
		$this->_trackAssets = true;
		parent::__construct();
	}

	public function cancel() {
		$this->setRedirect('index.php?option=com_surveyforce&view=surveys');
	}

	protected function postSaveHook(JModelLegacy $model, $validData = array()) {
		$this->last_insert_id = $model->getState($model->getName() . '.id');

		$model->updateField("result_desc", $validData["result_desc"], $this->last_insert_id);
	}

	public function save() {

		$task = JFactory::getApplication()->input->get('task');
		$save = parent::save();

		
	}


}
