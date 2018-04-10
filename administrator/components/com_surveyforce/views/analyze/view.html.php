<?php

/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class SurveyforceViewAnalyze extends JViewLegacy {

	protected $form;
	protected $table;
	protected $item;
	protected $fields;
	protected $layout;

	/**
	 * @param null $tpl
	 *
	 * @return bool
	 *
	 * @since version
	 */
	public function display($tpl = null) {

		$app           = JFactory::getApplication();
		$this->layout  = $app->input->getString("layout");
		$this->surv_id = $app->input->getInt("surv_id");

		if ($this->layout == 'edit') {
			SurveyforceHelper::showTitle('analyzes');

			$this->state  = $this->get('State');
			$this->form   = $this->get('Form');
			$this->table  = $this->get('Table');
			$this->item   = $this->get('Item');
			$this->fields = $this->get('Fields');

		} else {
			SurveyforceHelper::showTitle('analyzes_result');

			$this->result = $this->get('Result');
			$this->device = JHtml::_('utility.getDeviceCode');

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


		$isNew = ($this->item->id == 0);

		if ($this->layout == 'edit') {
			JToolBarHelper::apply('analyze.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('analyze.save', 'JTOOLBAR_SAVE');
			if ($isNew) {
				JToolBarHelper::cancel('analyze.cancel', 'JTOOLBAR_CANCEL');
			} else {
				JToolBarHelper::cancel('analyze.cancel', 'JTOOLBAR_CLOSE');
			}
		} else {
			JToolBarHelper::cancel('survey.cancel', 'JTOOLBAR_CLOSE');
		}

	}

}
