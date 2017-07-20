<?php

/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class SurveyforceControllerQuestions extends JControllerAdmin {

    public function __construct($config = array()) {
        parent::__construct($config);
        $this->registerTask('uncompulsory', 'compulsory');        
    }

    public function getModel($name = 'Questions', $prefix = 'SurveyforceModel') {

        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function add() {
        $this->setRedirect('index.php?option=com_surveyforce&task=question.add');
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

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $tmpl.'&surv_id='.JFactory::getApplication()->input->get('surv_id'), false));
    }

    public function compulsory() {
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $surv_id = JFactory::getApplication()->input->get('surv_id', 0);

        if (!is_array($cid) || count($cid) < 1) {
            JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
        } else {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            JArrayHelper::toInteger($cid);

            if ($model->compulsory($cid)) {
                $this->setMessage(JText::plural($this->text_prefix . '_COMPULSORED', count($cid)));
            } else {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=questions&surv_id=' . $surv_id, false));
    }

    public function publish() {
        $surv_id = JFactory::getApplication()->input->get('surv_id', 0);

        parent::publish();
        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=questions&surv_id=' . $surv_id, false));
    }

    public function edit() {
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $item_id = $cid['0'];
        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=question.edit&id=' . $item_id, false));
    }


	public function saveOrderAjax()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order, true);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
	
	public function move(){
	
		$cids = implode(',',JFactory::getApplication()->input->get('cid',array(),'array'));
		
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('s.*');
        $query->from('`#__survey_force_survs` as s');
		$db->setQuery($query);
		$survs = $db->loadObjectList();
		
		if($survs && $cids){
			?>
			<form action="index.php" method="POST">
				<label>
					<?php echo JText::_('COM_SURVEYFORCE_MOVE_TO'); ?>
				</label>
				<div>
					<select name="sf_id">
					<?php
					foreach($survs as $surv){
						echo '<option value="'.$surv->id.'">'.$surv->sf_name.'</option>';
					}
					?>
					</select>
				</div>
				<button class="btn btn-default"><?php echo JText::_('COM_SURVEYFORCE_MOVE_SUBMIT'); ?></button>
				<input type="hidden" name="questions" value="<?php echo $cids; ?>" />
				<input type="hidden" name="task" value="questions.moveto" />
				<input type="hidden" name="option" value="com_surveyforce" />
			</form>
			<?php
		}
	
	}
	
	public function moveto(){
	
		$input = JFactory::getApplication()->input;
		$sf = $input->get('sf_id',0);
		$ids = $input->get('questions','','string');
		
		if($sf && $ids){
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update('#__survey_force_quests');
			$query->set('`sf_survey` = "'.$sf.'"');
			$query->where('`id` IN ('.$ids.')');
			$db->setQuery($query);
			$db->execute();
		}
		
		JFactory::getApplication()->redirect('index.php?option=com_surveyforce&view=questions'.(($sf)?'&surv_id='.$sf:''));
	
	}

}
