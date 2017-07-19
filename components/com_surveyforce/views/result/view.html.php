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
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewResult extends JViewLegacy {

    public function __construct() {
        parent::__construct();
    }

    public function display($tpl = null) {

        $app = JFactory::getApplication();
		$this->itemid	= $app->input->getInt('Itemid');
		$this->survey_id	= $app->input->getInt('sid');
		
        $this->state = $this->get('state');
        $this->params = $this->state->get('params');
		
        $this->item = $this->get('Item');
		$this->orderby = $app->input->getInt('orderby');  // 排序方式
		if(!isset($this->orderby)) {
			$this->orderby = 0;
		}
		
		$model = $this->getModel();

		$this->device = JHtml::_('utility.getDeviceCode');
				

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        // 檢查
		$category_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$this->itemid}", false);

		// 檢查議題是否有效
		if ($this->item->id == 0) {
			$msg = "議題資料不存在，請重新選擇。";
			$app->redirect($category_link, $msg);
		}


		// 是否顯示結果
		switch($this->item->display_result) {
		    case 0:		// 不顯示
				$msg = "本議題不提供投票結果顯示";
		        break;
		    case 1:		// 投票中顯示
		        break;
		    case 2:		// 議題結束後才顯示
				$date = JFactory::getDate();
				$nowDate = $date->toSql();
				if ($this->item->vote_end > $nowDate) {		// 檢查是議題是否進行中
					$msg = "本議題於投票結束後才顯示投票結果";
				}

		        break;
		   
		}

		
		if ($msg) {
			$app->redirect($category_link, $msg);
		} else {
			$this->fields = $model->getFields($this->orderby);
			$this->sub_fields = $model->getSubFields($this->orderby);
			$this->results = $this->get('Results');
			$this->sub_results = $this->get('SubResults');
			$this->paper = $this->get('PaperResults');
			$this->sub_paper = $this->get('PaperSubResults');
			$this->place = $this->get('PlaceResults');
			$this->sub_place = $this->get('PlaceSubResults');

			$this->update_time =  $this->get('ResultsTime');
		}

		$document = JFactory::getDocument();
		$document->setTitle($this->escape($this->item->title));

		// Display the view
        parent::display($tpl);
        
    }

}
