<?php

/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die('Restricted access');

class SurveyforceControllerAnalyze extends JControllerForm {

	protected $last_insert_id;

	public function __construct() {
		parent::__construct();
	}

	public function cancel() {
		$this->setRedirect('index.php?option=com_surveyforce&view=analyzes');
	}

	protected function postSaveHook(JModelLegacy $model, $validData = array ()) {
		jimport('joomla.filesystem.folder');
		$app    = JFactory::getApplication();
		$config = JFactory::getConfig();

		$plugin        = JPluginHelper::getPlugin('system', 'switch');
		$exercise_host = json_decode($plugin->params, true);

		$ivoting_path      = $config->get('ivoting_path');
		$ivoting_save_path = $config->get('ivoting_save_path');
		$tmp_path          = $config->get('tmp_path');

		$this->last_insert_id = $model->getState($model->getName() . '.id');

		$post = $app->input->getArray($_POST);

		// 驗證後置處理

	}


	public function save() {
		$task = JFactory::getApplication()->input->get('task');
		$save = parent::save();
	}

	// 刪除
	public function delete() {
		$model     = $this->getModel();
		$app       = JFactory::getApplication();
		$jinput    = $app->input;
		$jform     = $app->input->get('jform', '', 'array');
		$survey_id = $jform['id'];

		$date    = JFactory::getDate();
		$nowDate = $date->toSql();

		$user    = JFactory::getUser();
		$user_id = $user->get('id');
		$unit_id = $user->get('unit_id');

		$survey = $model->getSurvey($survey_id);

		$created_user    = JFactory::getUser($survey->created_by);
		$created_unit_id = $created_user->get('unit_id');

		$state = $this->get('State');
		$canDo = JHelperContent::getActions('com_surveyforce');


		$self_gps    = JUserHelper::getUserGroups($user->get('id'));
		$core_review = JComponentHelper::getParams('com_surveyforce')->get('core_review');

		// 作者 或 同單位審核者 或 最高權限 才可儲存和刪除
		if ($survey->created_by == $user_id || ($unit_id == $created_unit_id && in_array($core_review, $self_gps)) || $canDo->get('core.own')) {
			// 是否為已投票
			if ($survey->complete && $survey->checked && (strtotime($survey->vote_start) < strtotime($nowDate)) && (strtotime($survey->vote_end) > strtotime($nowDate))) {
				JError::raiseWarning(100, '該議題正在進行投票中，無法進行刪除。');
			} else {
				$model->delete($survey_id);
				JError::raiseNotice(100, '議題刪除成功。');
			}
		} else {
			JError::raiseWarning(100, '權限不足，該議題無法進行刪除。');
		}


		$this->cancel();
	}

}
