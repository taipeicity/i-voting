<?php

/**
 * @package            Surveyforce
 * @version            1.2-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Category controller class.
 */
class SurveyforceControllerCategory extends JControllerForm {

	/**
	 * Proxy for getModel.
	 *
	 * @since    1.6
	 */
	public function getModel($name = 'category', $prefix = '', $config = array ('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);

		return $model;

	}

	public function category_completed() {

		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app     = JFactory::getApplication();
		$session = JFactory::getSession();

		$action = $app->input->getString("action");

		switch ($action) {
			case "radio":
				$condition = $app->input->getString("condition");
				$session->set('completed.radio', $condition);
				$session->clear('completed.search');
				break;
			case "search":
				$condition = $app->input->getString("survey_search");
				$session->set('completed.search', $condition);
				$session->clear('completed.radio');
				break;
			default:
				header("HTTP/1.0 404 Not Found");
				break;
		}

		$link = JRoute::_("index.php?option=com_surveyforce&view=category&layout=completed", false);
		$this->setRedirect($link);

	}

	public function category_soon() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app     = JFactory::getApplication();
		$session = JFactory::getSession();

		$condition = $app->input->getInt("condition");
		$session->set('soon.radio', $condition);

		$link = JRoute::_("index.php?option=com_surveyforce&view=category&layout=soon", false);
		$this->setRedirect($link);
	}

}
