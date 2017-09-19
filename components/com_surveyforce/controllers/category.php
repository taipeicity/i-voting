<?php

/**
 *   @package         Surveyforce
 *   @version           1.2-modified
 *   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 *   @license            GPL-2.0+
 *   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
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
	 * @since	1.6
	 */
	public function getModel($name = 'category', $prefix = '', $config = array ('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;

	}

	public function completed_form() {
		// 檢查Token
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$session = &JFactory::getSession();
		$session->clear('completed_form');

		$app = JFactory::getApplication();
		$params = $app->getParams();
		$completed_mymuid = $params->get('completed_mymuid');

		if (!$app->input->getVar('survey_search') && !$app->input->getInt('condition')) {
			$link = JRoute::_("index.php?option=com_surveyforce&view=category&layout=completed&Itemid=" . $completed_mymuid, false);
			$msg = "請輸入關鍵字。";
			$this->setRedirect($link, $msg);
			return;
		}

		unset($completed_form);

		$completed_form['search'] = $app->input->getVar('survey_search');
		$completed_form['condition'] = $app->input->getInt('condition');

		$session->set('completed_form', json_encode($completed_form));

		$this->setRedirect(JRoute::_("index.php?option=com_surveyforce&view=category&layout=completed&Itemid=" . $completed_mymuid, false));

	}

}
