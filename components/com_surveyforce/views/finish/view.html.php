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
class SurveyforceViewFinish extends JViewLegacy {

    public function __construct() {
        parent::__construct();
    }

    public function display($tpl = null) {
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();
		$this->itemid	= $app->input->getInt('Itemid');
		$this->survey_id	= $app->input->getInt('sid');
        
        
        $this->state = $this->get('state');
        $this->params = $this->state->get('params');



        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }


		// 檢查
		$category_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$this->itemid}", false);
		$intro_link = JRoute::_("index.php?option=com_surveyforce&view=intro&sid={$this->survey_id}&Itemid={$this->itemid}", false);


		// 檢查是否閒置過久
		if (SurveyforceVote::isSurveyExpired($this->survey_id) == false) {
			$msg = "網頁已閒置過久，請重新點選議題進行投票。";
			$app->redirect($category_link, $msg);
		}

		// 檢查議題是否有效
		if (SurveyforceVote::isSurveyValid($this->survey_id) == false) {
			$msg = "該議題目前未在可投票時間內，請重新選擇。";
			$app->redirect($category_link, $msg);
		}


		// 檢查是否有依序執行步驟
		if (SurveyforceVote::checkSurveyStep($this->survey_id, "finish") == false) {
			$msg = "該議題未從投票起始頁進入，請重新執行。";
			$app->redirect($intro_link, $msg);
		}

		$this->is_notice_email = SurveyforceVote::getSurveyData($this->survey_id, "is_notice_email");
		$this->is_notice_phone = SurveyforceVote::getSurveyData($this->survey_id, "is_notice_phone");
		$this->display_result = SurveyforceVote::getSurveyData($this->survey_id, "display_result");
		$this->is_lottery = SurveyforceVote::getSurveyData($this->survey_id, "is_lottery");

		 // Display the view
		$layout	= $app->input->getString('layout', 'default');
		$this->setLayout($layout);


		if ($layout == "default") {
			// 取得短網址
			$this->ticket_num = SurveyforceVote::getSurveyData($this->survey_id, "ticket");
			if ($this->ticket_num) {
				$vote_detail_url = JURI::root(). "vote_detail.php?ticket=". $this->ticket_num;
				$this->short_url = JHtml::_('utility.getShortUrl', $vote_detail_url);
				if ($this->short_url == "") {
					$this->short_url = JHtml::_('utility.getShortUrl2', $vote_detail_url);		// 呼叫第2組API

					if ($this->short_url == "") {
						sleep(1);
						$this->short_url = JHtml::_('utility.getShortUrl3', $vote_detail_url);		// 呼叫第3組API
						if ($this->short_url == "") {
							$this->short_url = $vote_detail_url;
						}
					}
				}
			} else {
				$this->short_url = "";
				JFactory::getApplication()->enqueueMessage("未正確取得票號，請重新投票");
			}
			// 記錄短網址
			SurveyforceVote::setSurveyData($this->survey_id, "short_url", $this->short_url);
		}
		
		if(!$this->item) {
			$this->item = $this->get('Item');	
		}
		$document = JFactory::getDocument();
		$document->setTitle($this->escape($this->item->title));

		

        parent::display($tpl);
        
    }

}
