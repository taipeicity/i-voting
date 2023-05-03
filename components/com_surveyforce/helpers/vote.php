<?php

/**
 * @package            Surveyforce
 * @version            1.2-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
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
		$query->from($db->quoteName('#__survey_force_survs_release') . ' AS a');
		$query->where('a.id = ' . (int) $_survey_id);
		$query->where('a.published = 1');
		$query->where('a.is_complete = 1');
		$query->where('a.is_checked = 1');


		// Filter by publish
		$nullDate = $db->Quote($db->getNullDate());
		$date     = JFactory::getDate();
		$nowDate  = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		$db->setQuery($query);

		return $db->loadObject();

	}

	// 檢查投票是否有效 (時間、發佈...)
	public static function isSurveyValid($_survey_id) {
		$db      = JFactory::getDBO();
		$query   = $db->getQuery(true);
		$session = JFactory::getSession();

		$query->select('a.id, a.vote_pattern');
		$query->from($db->quoteName('#__survey_force_survs_release') . ' AS a');
		$query->where('a.published = 1');
		$query->where('a.is_checked = 1');
		$query->where('a.id = ' . (int) $_survey_id);


		// Filter by publish date
		$nullDate = $db->Quote($db->getNullDate());
		$date     = JFactory::getDate();
		$nowDate  = $db->Quote($date->toSql());
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

		if ($session->get('practice')) { // 判斷投票模式
			$pattern = "practice";
		} else {
			$pattern = "formal";
		}

		// 檢查是否透過轉換投票模式避開正常步驟
		$survey_session = json_decode($session->get($pattern . "_survey_step_" . $_survey_id), true);

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

			case "token":
				if ($survey_session["token"] == false) {
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

			case "lottery":
				if ($survey_session["lottery"] == false) {
					return false;
				}
				break;

			case "join_lottery":
				if ($survey_session["join_lottery"] == false) {
					return false;
				}
				break;

			case "check_column":
				if ($survey_session["check_column"] == false) {
					return false;
				}
				break;

			case "success":
				if ($survey_session["success"] == false) {
					return false;
				}
				break;

			case "resend":
				if ($survey_session["resend"] == false) {
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


		if ($session->get('practice')) { // 判斷投票模式
			$pattern = "practice";
		} else {
			$pattern = "formal";
		}

		if ($_init == true) { // 初始化
			$session->clear($pattern . "_survey_step_" . $_survey_id); // 先清空
			$survey_session                 = array ();
			$survey_session["intro"]        = false;  // 開始頁
			$survey_session["token"]        = false;  // 未公開議題token
			$survey_session["verify"]       = false;  // 驗證頁
			$survey_session["statement"]    = false;  // 個資頁
			$survey_session["question"]     = false;  // 問題頁
			$survey_session["finish"]       = false;  // 結束
			$survey_session["email"]        = false;  // 寄送短網址
			$survey_session["lottery"]      = false;  // 抽獎頁面
			$survey_session["join_lottery"] = false;  // 已參加抽獎
			$survey_session["check_column"] = false;  // 留存頁
			$survey_session["resend"]       = false;  // 補送抽獎頁


			$survey_session[$_step] = true;  // 指定頁
		} else {
			$survey_session         = json_decode($session->get($pattern . "_survey_step_" . $_survey_id), true);
			$survey_session[$_step] = true;  // 指定頁
		}

		$session->set($pattern . "_survey_step_" . $_survey_id, json_encode($survey_session));

		return true;

	}

	// 取回議題Session的資料
	public static function getSurveyData($_survey_id, $_name) {
		$session = &JFactory::getSession();

		$survey_session = [];
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

    /**
     * @param $_url
     * @param $_method
     * @param $_param
     * @param bool $join 是否join平台
     * @param bool $taipeiCard 是否台北通
     *
     * @return bool|string
     *
     * @since version
     */
    public static function curlAPI($_url, $_method, $_param, $join = false, $taipeiCard = false) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		switch ($_method) {
			case "GET":
				if ($join) {
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				}
				$_url .= '?' . http_build_query($_param);
				break;
			case "POST":
				curl_setopt($ch, CURLOPT_POST, true);
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

        if (!$taipeiCard) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        }
        curl_setopt($ch, CURLOPT_URL, $_url);
		$api_response = curl_exec($ch);
		$code         = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$message      = curl_error($ch);

		curl_close($ch);

		if ($code == 200) {
			return $api_response;
		} else {
			// 記錄log
			JHtml::_('utility.recordLog', "api_log.php", sprintf("Url:%s, Code:%d, Method:%s, Msg:%s", $_url, $code, $_method, $message), JLog::ERROR);

			return false;
		}

	}

	// 檢查是否已閒置過久
	public static function isSurveyExpired($_survey_id) {
		$app           = JFactory::getApplication();
		$layout        = $app->input->getString('layout', 'default');
		$config        = JFactory::getConfig();
		$expire_minute = $config->get('expire_minute', 30);

		if ($layout == 'resend') {
			$expire_minute = 10;
		}

		$expire_time = self::getSurveyData($_survey_id, "expire_time");

		// 若未超過閒置時間，則以目前時間再往後延長
		if (time() <= $expire_time) {
			self::setSurveyData($_survey_id, "expire_time", time() + ($expire_minute * 60));

			return true;
		} else {
			return false;
		}

	}

	public static function checkVotePattern($surv_id) {
		$session      = &JFactory::getSession();
		$vote_pattern = self::getSurveyData($surv_id, "vote_pattern");

		$result = [
			'msg'    => '',
			'status' => 0
		];

		if ($session->get('practice')) {
			if ($vote_pattern == "1") {
				$result['msg']    = "該議題未開放投票練習，請重新操作。";
				$result['status'] = 1;
			}
		} else {
			if ($vote_pattern == "2") {
				$result['msg']    = "該議題未開放正式投票，請重新操作。";
				$result['status'] = 1;
			}
		}

		return json_encode($result);
	}

	public static function ReplacePath($path) {

		$session = &JFactory::getSession();

		if ($session->get('practice')) {

			$uri      = JUri::getInstance();
			$uri_root = $uri->toString(array (
					'scheme',
					'host',
					'port'
				)) . "/";

			return $uri_root . $path;
		} else {
			return JUri::root() . $path;
		}

	}

	// 取得前台問題頁的分析欄位
	public static function getAnalyzeColumn($survey_id) {

		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('a.surv_id, a.required, c.quest_id AS qid, c.quest_title, c.field_id AS fid, c.field_title');
		$query->from($db->quoteName('#__survey_force_analyze', 'a'));
		$query->join('LEFT', $db->quoteName('#__survey_force_analyze_count', 'c') . ' ON ' . $db->quoteName('c.quest_id') . ' = ' . $db->quoteName('a.quest_id'));
		$query->where($db->quoteName('a.surv_id') . ' = ' . $db->quote($survey_id));
		$query->where($db->quoteName('c.survey_id') . ' = ' . $db->quote($survey_id));
		$query->where($db->quoteName('a.publish') . ' = ' . $db->quote(1));
		$query->order($db->quoteName('c.quest_id'));
		$query->order($db->quoteName('c.field_id'));

		$db->setQuery($query);
		$results = $db->loadObjectList();

		$i       = 0;
		$rows    = [];
		$selects = [];
		foreach ($results as $result) { //將欄位依照題目分類
			if ($rows[$result->quest_title][$i]['qid'] == $result->qid) {
				$i++;
			} else {
				$i = 0;
			}
			$rows[$result->quest_title][$i]['surv_id']     = $result->surv_id;
			$rows[$result->quest_title][$i]['required']    = $result->required;
			$rows[$result->quest_title][$i]['qid']         = $result->qid;
			$rows[$result->quest_title][$i]['fid']         = $result->fid;
			$rows[$result->quest_title][$i]['field_title'] = $result->field_title;
		}

		foreach ($rows as $key => $row) {
			unset($options);
			foreach ($row as $num => $item) {
				if ($num == 0) {
					$options[] = JHtml::_('Select.option', 0, '請選擇');
				}
				$options[] = JHtml::_('Select.option', $item['fid'], $item['field_title'], ['option.attr' => 'align="center"']);
				$qid       = $item['qid'];
			}
			$selects[$key]['select']   = JHtml::_('Select.genericlist', $options, 'analyze_' . $qid);
			$selects[$key]['qid']      = 'analyze_' . $qid;
			$selects[$key]['required'] = $item['required'];
		}

		return $selects;

	}

	public static function checkJoinLottery($_ticket_num, $_survey_id) {


		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__survey_force_vote'));
		$query->where($db->quoteName('ticket_num') . ' = ' . $db->quote($_ticket_num));
		$query->where($db->quoteName('survey_id') . ' = ' . $db->quote($_survey_id));
		$query->where($db->quoteName('is_lottery') . ' = ' . $db->quote(1));

		$db->setQuery($query);

		if ($db->loadObject()) {
			return true;
		} else {
			return false;
		}

	}

	public static function getUnPublicSurveyItem($_survey_id) {
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from($db->quoteName('#__survey_force_survs_release') . ' AS a');
		$query->where('a.id = ' . (int) $_survey_id);
		$query->where('a.published = 1');
		$query->where('a.is_checked = 1');


		// Filter by publish
		$nullDate = $db->Quote($db->getNullDate());
		$date     = JFactory::getDate();
		$nowDate  = $db->Quote($date->toSql());
		$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
		$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

		$db->setQuery($query);

		return $db->loadObject();

	}

	public static function getVerifyName($verify_type) {
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('name, element');
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$query->where($db->quoteName('access') . ' = ' . $db->quote(1));
		$query->where($db->quoteName('enabled') . ' = ' . $db->quote(1));
		$query->where($db->quoteName('folder') . ' = ' . $db->quote('verify'));
		$query->order('ordering');

		$db->setQuery($query);
		$items = $db->loadAssocList('element');

		unset($auths);
		$auths = array ();
		foreach ($verify_type as $type) {
			$auths[] = $items[$type]["name"];
		}

		return json_encode($auths);
	}

	public static function getSurvsCounts($cat) {

		if (!is_string($cat)) {
			throw new InvalidArgumentException('Argument 1 should be string.');
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->qn('#__survey_force_survs_release'));
		$query->where($db->qn('published') . ' = 1');
		$query->where($db->qn('is_checked') . ' = 1');
		$query->where($db->qn('is_public') . ' = 1');
		$query->where($db->qn('survey_type') . ' = 1');	// 篩選議題類型只取 ivoting
		$query->where($db->qn('is_whitelist') . ' = 0');	// 沒有啟用白名單

		$date    = JFactory::getDate();
		$nowDate = $db->q($date->toSql());

		switch ($cat) {
			//case 'proposal':   // 我要提案
			//	$filter = [
			//		1
			//	];
			//	break;

            case 'reconsideration': // 我要覆議
                $filter = [
                    1, 2
                ];
                break;

			case 'discuss':   // 我要討論
				$filter = [
					3,
					4
				];
				break;
			case 'voting':    // 我要投票
				$filter = [
					5,
					6
				];
				$query->where($db->qn('vote_end') . ' >= ' . $nowDate);
				break;
			case 'complete':   // 已完成投票
				$filter = [6];
				$query->where($db->qn('vote_end') . ' <= ' . $nowDate);
				break;
		}

		$nullDate = $db->Quote($db->getNullDate());
		$query->where('(publish_up = ' . $nullDate . ' OR publish_up <= ' . $nowDate . ')');
		$query->where('(publish_down = ' . $nullDate . ' OR publish_down >= ' . $nowDate . ')');

		$query->where($db->qn('stage') . ' IN (' . implode(",", $db->q($filter)) . ')');
		$query->where($db->qn('vote_pattern') . ' IN (1,3)');
		$query->where($db->qn('is_define') . ' = 1');


		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return count($rows);
	}

	public static function autoInput($string, $a_tag = false, $id = null) {
		if (!empty($string) and $a_tag) {
			$a      = '<a href="javascript:void(0)" class="getPdf" id="' . $id . '" title="' . $string . '">';
			$a      .= $string;
			$a      .= '</a>';
			$string = $a;
		}

		return empty($string) ? "無資料" : $string;
	}

}
