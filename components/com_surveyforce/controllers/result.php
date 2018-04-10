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
 * Result controller class.
 */
class SurveyforceControllerResult extends JControllerForm {

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'result', $prefix = '', $config = array ('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;

	}

	public function SetOrderBy() {

		$session = &JFactory::getSession();
		$app = JFactory::getApplication();
		$Itemid = $app->input->getInt('Itemid');
		$sid = $app->input->getInt('sid');
		$orderby = $app->input->getInt('orderby');

		if ($session->get('practice_pattern')) {
			$pattern = "practice";
		}else{
			$pattern = "formal";
		}

		$session->clear($pattern . '_orderby');
		$session->set($pattern . '_orderby', $orderby);

		$this->setRedirect(JRoute::_("index.php?option=com_surveyforce&view=result&sid={$sid}&Itemid={$Itemid}", false));

	}

	public function SetChart() {

		$session = &JFactory::getSession();
		$app = JFactory::getApplication();
		$Itemid = $app->input->getInt('Itemid');
		$sid = $app->input->getInt('sid');
		$chart = $app->input->getVar('chart');

		if ($session->get('practice_pattern')) {
			$pattern = "practice";
		}else{
			$pattern = "formal";
		}

		$session->clear($pattern . '_chart');
		$session->set($pattern . '_chart', $chart);

		$this->setRedirect(JRoute::_("index.php?option=com_surveyforce&view=result&sid={$sid}&Itemid={$Itemid}", false));

	}

}
