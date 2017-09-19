<?php

/**
 *   @package         Surveyforce
 *   @version           1.2-modified
 *   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 *   @license            GPL-2.0+
 *   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML Template class for the Surveyforce Deluxe Component
 */
class SurveyforceVote {

	public function __construct() {
		
	}

	// 取得議題資料
	public static function getSurveyItem($_survey_id) {
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from($db->quoteName('#__survey_force_survs') . ' AS a');
		$query->where('a.id = ' . (int) $_survey_id);
		$query->where('a.published = 1');
		$query->where('a.is_complete = 1');
		$query->where('a.is_checked = 1');


		// Filter by publish
		$nullDate = $db->Quote($db->getNullDate());
		$date = JFactory::getDate();
		$nowDate = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		$db->setQuery($query);

		return $db->loadObject();

	}

	// 檢查投票是否有效 (時間、發佈...)
	public static function isSurveyValid($_survey_id) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);


		$query->select('a.id');
		$query->from($db->quoteName('#__survey_force_survs') . ' AS a');
		$query->where('a.published = 1');
		$query->where('a.is_checked = 1');
		$query->where('a.id = ' . (int) $_survey_id);



		// Filter by publish date
		$nullDate = $db->Quote($db->getNullDate());
		$date = JFactory::getDate();
		$nowDate = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		// 進行中
		$query->where('a.vote_start <= ' . $nowDate);
		$query->where('a.vote_end >= ' . $nowDate);

		$db->setQuery($query);
		if ($db->loadObject()) {
			return true;
		} else {
			return false;
		}

	}

	// 檢查是否有執行議題中的每個步驟
	public static function checkSurveyStep($_survey_id, $_step) {
		$session = &JFactory::getSession();

		unset($survey_session);

		// 檢查是否透過轉換投票模式避開正常步驟
		if ($session->get("practice_pattern") === false) {
			$survey_session = json_decode($session->get('formal_survey_step_' . $_survey_id), true);
		} else {
			$survey_session = json_decode($session->get('practice_survey_step_' . $_survey_id), true);
		}

		if (!$survey_session) {
			return false;
		}

		switch ($_step) {
			case "practice":
				if ($survey_session["practice"] == false) {
					return false;
				}
				break;

			case "formal":
				if ($survey_session["formal"] == false) {
					return false;
				}
				break;

			case "statement":
				if ($survey_session["intro"] == false) {
					return false;
				}
				break;

			case "verify":
				if ($survey_session["intro"] == false || $survey_session["statement"] == false) {
					return false;
				}
				break;

			case "question":
				if ($survey_session["intro"] == false || $survey_session["statement"] == false || $survey_session["verify"] == false) {
					return false;
				}
				break;

			case "finish":
				if ($survey_session["question"] == false) {
					return false;
				}
				break;

			case "email":
				if ($survey_session["email"] == false) {
					return false;
				}
				break;

			case "all":
				if ($survey_session["intro"] == false || $survey_session["statement"] == false || $survey_session["verify"] == false || $survey_session["question"] == false || $survey_session["finish"]) {
					return false;
				}
				break;

			default:
				return false;
				break;
		}

		return true;

	}

	// 設定議題的每個步驟
	public static function setSurveyStep($_survey_id, $_step, $_init = false) {
		$session = &JFactory::getSession();

		unset($survey_session);

		if ($_init == true) { // 初始化
			$session->clear('formal_survey_step_' . $_survey_id); // 先清空
			$session->clear('practice_survey_step_' . $_survey_id); // 先清空
			$survey_session = array ();
			$survey_session["intro"] = false;  // 開始頁	
			$survey_session["verify"] = false;  // 驗證頁
			$survey_session["statement"] = false; // 個資頁
			$survey_session["question"] = false; // 問題頁
			$survey_session["finish"] = false;  // 結束
			$survey_session["email"] = false;  // 寄送短網址


			$survey_session[$_step] = true;  // 指定頁
		} else {
			if ($session->get("practice_pattern") === false) {
				$survey_session = json_decode($session->get('formal_survey_step_' . $_survey_id), true);
			} else {
				$survey_session = json_decode($session->get('practice_survey_step_' . $_survey_id), true);
			}
			$survey_session[$_step] = true;  // 指定頁
		}

		if ($session->get("practice_pattern") === false) {
			$session->Set('formal_survey_step_' . $_survey_id, json_encode($survey_session));
		} else {
			$session->Set('practice_survey_step_' . $_survey_id, json_encode($survey_session));
		}

		return true;

	}

	// 取回議題Session的資料
	public static function getSurveyData($_survey_id, $_name) {
		$session = &JFactory::getSession();

		unset($survey_session);
		$survey_session = json_decode($session->get('survey_data_' . $_survey_id), true);

		return $survey_session[$_name];

	}

	// 設定議題Session的資料
	public static function setSurveyData($_survey_id, $_name, $_value, $_init = false) {
		$session = &JFactory::getSession();

		unset($survey_session);

		if ($_init == true) { // 初始化
			$session->clear('survey_data_' . $_survey_id); // 先清空
			$survey_session = array ();
		} else {
			$survey_session = json_decode($session->get('survey_data_' . $_survey_id), true);
		}



		$survey_session[$_name] = $_value;  // 給予變數值
		$session->Set('survey_data_' . $_survey_id, json_encode($survey_session));

		return true;

	}

	// 取得網頁結果
	public static function curlAPI($_url, $_method, $_param) {
		unset($result);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		switch ($_method) {
			case "GET":
				$_url .= '?' . http_build_query($_param);
				break;
			case "POST":
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_param));
				break;
			case "PUT":
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_param));
				break;
			case "DELETE":
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_param));
				break;

			default:
				break;
		}



		curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Accept: application/json'));
		curl_setopt($ch, CURLOPT_URL, $_url);
		$api_response = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$message = curl_error($ch);

		curl_close($ch);

		if ($code == 200) {
			return $api_response;
		} else {
			// 記錄log
			JHtml::_('utility.recordLog', "api_log.php", sprintf("Url:%s, Code:%d, Msg:%s", $_url, $code, $message), JLog::ERROR);
			return false;
		}

	}

	// 檢查是否已閒置過久
	public static function isSurveyExpired($_survey_id) {
		$config = JFactory::getConfig();
		$expire_minute = $config->get('expire_minute', 30);

		$expire_time = self::getSurveyData($_survey_id, "expire_time");

		// 若未超過閒置時間，則以目前時間再往後延長
		if (time() <= $expire_time) {
			self::setSurveyData($_survey_id, "expire_time", time() + ($expire_minute * 60));
			return true;
		} else {
			return false;
		}

	}

}
