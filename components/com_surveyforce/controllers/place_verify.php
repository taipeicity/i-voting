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
 * Place_verify controller class.
 */
class SurveyforceControllerPlace_verify extends JControllerForm {
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */

	public function getModel($name = 'place_verify', $prefix = '', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function check_verify_form() {
		$model = $this->getModel();
		
		$session 	= &JFactory::getSession();
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		$survey_id = $session->get('place_survey_id');
		$itemid	= $app->input->getInt('Itemid', 0);
		$client_ip = JHtml::_('utility.getUserIP');



		// 檢查欄位是否已填寫
		$login_link = JRoute::_("index.php?option=com_surveyforce&view=place_login&Itemid={$itemid}", false);
		$return_link = JRoute::_("index.php?option=com_surveyforce&view=place_verify&sid={$survey_id}&Itemid={$itemid}", false);

		unset($msges);
		$msges = array();

		$idnum = strtoupper($app->input->getString('idnum'));

		if ($idnum == "") {
			$msges[] = "請輸入身分證字號。";
		}


		if (count($msges) > 0) {
			$this->setRedirect($return_link, implode("<br>", $msges));
			return;
		}


		// 檢查是否有登入
		if (!$session->get('place_username')) {
			$msg = "您尚未登入，請重新登入。";
			$app->redirect($login_link, $msg);
		}



		// 檢查是否已投過票
		$agent_path = $config->get( 'agent_path' );
		unset($api_request_url);
		unset($api_request_parameters);
		$api_request_url = $agent_path. "/server_poll.php";

		$verify_params = SurveyforceVote::getSurveyData($survey_id, "verify_params");
		$vote_num_params = SurveyforceVote::getSurveyData($survey_id, "vote_num_params");
		$api_request_parameters = array(
			'survey_id' => $survey_id,
			'identify' => $idnum,
			'verify_type' => "idnum",
			'vote_num_params' => $vote_num_params
		);

		$api_result = SurveyforceVote::curlAPI($api_request_url, "GET", $api_request_parameters);
		if ( $api_result == "") {
			$msg = "無法執行，請重新操作。";
			$this->setRedirect($return_link, $msg);
			return;

		} else {
			$decode_data = json_decode($api_result);

			if ($decode_data->status == 1) {
				$msg = "該資料已投過票，請重新輸入。";
				$this->setRedirect($return_link, $msg);
				return;

			}
		}


		// 驗證資料是否正確
		JPluginHelper::importPlugin('verify', 'idnum');
		$result_check_data = json_decode( plgVerifyIdnum::onVerifyDataPlace($survey_id, $idnum, $verify_params) );
		if ($result_check_data->status == 0) {		// 驗證失敗
			// 記錄驗證結果至DB
			$model->recordVerifyStatus($survey_id, "entity", $result_check_data->status, $client_ip);

			$msg = "資料驗證失敗，請重新輸入驗證資料。";
			$this->setRedirect($return_link, $msg);
			return;
		}  else if ($result_check_data->status == 1) {		// 驗證成功

			// 記錄驗證結果至DB
			$model->recordVerifyStatus($survey_id, "entity", $result_check_data->status, $client_ip);

			// 寫入驗證後的身分證字號別至session
			$session->Set('place_verify_idnum', $idnum);
		}



		// 設定已通過verify步驟
		$session->Set('place_verify', true);
		

		$link = JRoute::_("index.php?option=com_surveyforce&view=place_question&sid={$survey_id}&Itemid={$itemid}", false);

		$this->setRedirect($link);
		return;
	}




}