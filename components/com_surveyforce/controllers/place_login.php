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
 * Place_login controller class.
 */
class SurveyforceControllerPlace_login extends JControllerForm {
	/*	 * recaptcha
	 * Proxy for getModel.
	 * @since	1.6
	 */

	public function getModel($name = 'place_login', $prefix = '', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function check_login_form() {
		$session = &JFactory::getSession();
		$app = JFactory::getApplication();
		$params = $app->getParams();


		// 限制IP連線
		if ($params->get('is_limit_connection')) {
			$user_ip = JHtml::_('utility.getUserIP');
			$is_allow = false;

			// 單一IP
			if ($params->get('single_ips')) {
				$single_ips = explode(",", $params->get('single_ips'));

				if (in_array($user_ip, $single_ips)) {
					$is_allow = true;
				}
			}

			// IP範圍
			if ($params->get('range_ips')) {
				$range_ips = explode(",", $params->get('range_ips'));
				foreach ($range_ips as $ips) {
					$ipdata = explode("-", $ips);
					if (ip2long($user_ip) <= ip2long($ipdata[1]) && ip2long($ipdata[0]) <= ip2long($user_ip)) {
						$is_allow = true;
					}
				}
			}

			if ($is_allow == false) {
				JError::raiseError(404);
				jexit;
			}
		}

		// 取得工作人員群組
		$group_id = $params->get('group_id');
		$itemid = $app->input->getInt('Itemid', 0);

		// 檢查欄位是否已填寫
		$return_link = JRoute::_("index.php?option=com_surveyforce&view=place_login&Itemid={$itemid}", false);
		unset($msges);
		$msges = array();

		$username = trim($app->input->getString('username'));
		$passwd = trim($app->input->getString('passwd'));


		if ($username == "") {
			$msges[] = "請輸入帳號。";
		}

		if ($passwd == "") {
			$msges[] = "請輸入密碼。";
		}


		// 檢查驗證碼
		$captcha = $app->input->getString('recaptcha_response_field2');
		if ($captcha) {
			// 與session中的值做比對
			if ($session->get('captcha_0') == md5($captcha)) {
				// 比對正確則清空session
				$session->Set('captcha_0', "");
			} else {
				$msges[] = "驗證碼比對錯誤，請重新填寫。";
			}
		} else {
			$msges[] = "請填寫驗證碼。";
		}


		if (count($msges) > 0) {
			$this->setRedirect($return_link, implode("<br>", $msges));
			return;
		}

		// 檢查帳號密碼
		$result = json_decode($this->checkLogin($username, $passwd, $group_id));
		if ($result->status == 0) {
			$this->setRedirect($return_link, $result->msg);
			return;
		} else {
			// 記錄已登入
			$session->Set('place_username', $username);
		}

		$link = JRoute::_("index.php?option=com_surveyforce&view=place_category&Itemid={$itemid}", false);

		$this->setRedirect($link);
		return;
	}

	public function checkLogin($_username, $_passwd, $_allow_group_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
				->select('id, password')
				->from('#__users')
				->where('block = 0')
				->where('username=' . $db->quote($_username));

		$db->setQuery($query);
		$row = $db->loadObject();


		if ($row) {
			$match = JUserHelper::verifyPassword($_passwd, $row->password, $row->id);

			if ($match === true) {
				// 限定群組
				$user = JUser::getInstance($row->id);
				$groups = $user->get('groups');


				// 檢查是否在允許的群組中
				if (in_array($_allow_group_id, $groups)) {
					JHtml::_('utility.recordLog', "place_log.php", sprintf("%s登入成功", $_username), JLog::ERROR);

					$result = array("status" => 1, "msg" => "");
				} else {
					JHtml::_('utility.recordLog', "place_log.php", sprintf("%s登入失敗，非指定群組", $_username), JLog::ERROR);

					$result = array("status" => 0, "msg" => "登入失敗，非指定群組。");
				}
			} else {
				JHtml::_('utility.recordLog', "place_log.php", sprintf("%s登入失敗，密碼不相符", $_username), JLog::ERROR);

				$result = array("status" => 0, "msg" => "登入失敗，請重新登入。");
			}
		} else {
			JHtml::_('utility.recordLog', "place_log.php", sprintf("%s登入失敗，查無該帳號", $_username), JLog::ERROR);

			$result = array("status" => 0, "msg" => "登入失敗，請重新登入。");
		}

		return json_encode($result);
	}

}