<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class SurveyforceControllerSurveys extends JControllerAdmin {

    public function __construct($config = array()) {
        parent::__construct($config);
    }

    public function getModel($name = 'Surveys', $prefix = 'SurveyforceModel') {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function add() {
        $this->setRedirect('index.php?option=com_surveyforce&task=survey.add');
    }

    public function delete() {
        // Get items to remove from the request.
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $tmpl = JFactory::getApplication()->input->get('tmpl');
        if ($tmpl == 'component')
            $tmpl = '&tmpl=component';
        else
            $tmpl = '';

        if (!is_array($cid) || count($cid) < 1) {
            JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
        } else {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            JArrayHelper::toInteger($cid);

            // Remove the items.
            if ($model->delete($cid)) {
                $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
            } else {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $tmpl, false));
    }

    public function edit() {
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $item_id = $cid['0'];
        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=survey.edit&id=' . $item_id, false));
    }

	public function preview()
	{
		$database = JFactory::getDbo();
		$cid = (int) end( JFactory::getApplication()->input->get('cid', array(), 'array') );

		$unique_id = md5(uniqid(rand(), true));
		$query = "INSERT INTO `#__survey_force_previews` SET `preview_id` = '".$unique_id."', `time` = '".strtotime(JFactory::getDate())."'";
		$database->setQuery( $query );
		$database->query( );

		$this->setRedirect( JRoute::_(JUri::root()."index.php?option=com_surveyforce&view=survey&id={$cid}&preview=".$unique_id) );
	}

}
