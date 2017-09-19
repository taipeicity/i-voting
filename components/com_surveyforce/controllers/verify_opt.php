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
 * Verify_opt controller class.
 */
class SurveyforceControllerVerify_opt extends JControllerForm {
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */

	public function getModel($name = 'verify', $prefix = '', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	// 檢查表單欄位
	public function check_verify_form() {
		$config = JFactory::getConfig();
		$session 	= &JFactory::getSession();
		$app = JFactory::getApplication();
		$params	= $app->getParams();

		$model = $this->getModel();


		$survey_id	= $app->input->getInt('sid', 0);
		$itemid	= $app->input->getInt('Itemid', 0);
		
		$client_ip = JHtml::_('utility.getUserIP');

		$category_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$itemid}", false);
		$intro_link = JRoute::_("index.php?option=com_surveyforce&view=intro&sid={$survey_id}&Itemid={$itemid}", false);


		// 檢查是否閒置過久
		if (SurveyforceVote::isSurveyExpired($survey_id) == false) {
			$msg = "網頁已閒置過久，請重新點選議題進行投票。";
			$this->setRedirect($category_link, $msg);
			return;
		}


		// 檢查議題是否有效
		if (SurveyforceVote::isSurveyValid($survey_id) == false) {
			$msg = "該議題目前未在可投票時間內，請重新選擇。";
			$this->setRedirect($category_link, $msg);
			return;
		}

		// 檢查是否有中途更換議題
		if (SurveyforceVote::checkSurveyStep($survey_id, "verify") == false) {
			$msg = "該議題未從投票啟始頁進入，請重新執行。";
			$this->setRedirect($intro_link, $msg);
			return;
		}



		// 檢查欄位是否已填寫
		$return_link = JRoute::_("index.php?option=com_surveyforce&view=verify&sid={$survey_id}&Itemid={$itemid}", false);
		unset($msges);
		$msges = array();


		// 取出該議題的驗證方式
		$verify_required = SurveyforceVote::getSurveyData($survey_id, "verify_required");
		$survey_verify_types = SurveyforceVote::getSurveyData($survey_id, "verify_type");


		$post = $app->input->getArray($_POST);

		// 檢查所選的驗證方式是否有被修改
		if ($survey_verify_types) {
			$verify_types = json_decode($survey_verify_types, true);		// 取得該議題所有驗證方式

			unset($select_verify_types);
			if ($verify_required) {	// 同時驗證
				$select_verify_types = $verify_types;
			} else {	// 擇一驗證
				$type = $app->input->getString('verify_type');
				if ( in_array($type, $verify_types) ) {
					$select_verify_types = array( $type );
				} else {
					$msges[] = "該驗證方式並不存在，請重新選擇。";
				}

			}

			// 記錄選擇驗證的方式
			SurveyforceVote::setSurveyData($survey_id, "opt_verify_types", $select_verify_types);


		} else {
			$this->setRedirect($intro_link, "驗證方式失效，請重新操作。");
			return;
		}



		if (count($msges) > 0) {
			$this->setRedirect($return_link, implode("<br>", $msges));
			return;
		}


		// 進入驗證資料頁
		$link = JRoute::_("index.php?option=com_surveyforce&view=verify&sid={$survey_id}&Itemid={$itemid}", false);
		$this->setRedirect($link);

	}


}
