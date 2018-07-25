<?php

/**
 * @package            Surveyforce
 * @version            1.3-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die;

/**
 * Surveyforce component helper.
 */
class SurveyforceHelper {

	public static function addSubmenu($vName = 'surveys') {
		JHtmlSidebar::addEntry(JText::_('COM_SURVEYFORCE_SURVEYS_LIST'), 'index.php?option=com_surveyforce&view=surveys', $vName == 'surveys');
		JHtmlSidebar::addEntry(JText::_('COM_SURVEYFORCE_SURVEYS_ANALYZES'), 'index.php?option=com_surveyforce&view=analyzes', $vName == 'analyzes');
	}


	public static function showTitle($submenu, $addition = false) {
		$document = JFactory::getDocument();
		$title    = JText::_('COM_SURVEYFORCE_' . strtoupper($submenu));
		$document->setTitle($title . ($addition ? ' ' . $addition : ''));
		JToolBarHelper::title($title . ($addition ? ' ' . $addition : ''), $submenu);

		return $title;
	}

	public static function getCSSJS() {
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root() . 'administrator/components/com_surveyforce/assets/css/surveyforce.css');
	}


	static function sfGetOrderingList($sql, $chop = '55') {
		$database = JFactory::getDBO();

		$order = array ();
		$database->setQuery($sql);
		if (!($orders = ($database->LoadObjectList() == null ? array () : $database->LoadObjectList()))) {
			if ($database->getErrorNum()) {
				echo $database->stderr();

				return false;
			} else {
				$order[] = JHTML::_('select.option', 1, JText::_('COM_SURVEYFORCE_FIRST'));

				return $order;
			}
		}
		$order[] = JHTML::_('select.option', 0, '0 ' . JText::_('COM_SURVEYFORCE_FIRST'));
		for ($i = 0, $n = count($orders); $i < $n; $i++) {
			$orders[$i]->text = strip_tags($orders[$i]->text);
			if (strlen($orders[$i]->text) > $chop) {
				$text = substr($orders[$i]->text, 0, $chop) . "...";
			} else {
				$text = $orders[$i]->text;
			}

			$order[] = JHTML::_('select.option', $orders[$i]->value, $orders[$i]->value . ' (' . $text . ')');
		}
		$order[] = JHTML::_('select.option', $orders[$i - 1]->value + 1, ($orders[$i - 1]->value + 1) . JText::_('COM_SURVEYFORCE_LAST'));

		return $order;
	}


	public static function getSuveryItem($_survey_id) {
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__survey_force_survs_release');
		$query->where('id = ' . (int) $_survey_id);

		$db->setQuery($query);

		return $db->loadObject();
	}

	public static function replaceUrl($content) {

		//用正規找出所有超連結
		preg_match_all("/\<a.+href\=\"(.+)\".*\>(.+)\<\/a>/", $content, $part_content);

		//組合新的陣列
		$replace = [];
		foreach ($part_content as $key => $items) {
			foreach ($items as $key2 => $item) {
				if ($key == 0) {
					$replace[$item] = $part_content[2][$key2] . '(' . $part_content[1][$key2] . ')';
				}
			}
		}

		//替換字串
		foreach ($replace as $key => $item) {
			$content = str_replace($key, $item, $content);
		}

		return $content;
	}

	public static function checkAnalyze($id) {
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__survey_force_analyze'));
		$query->where($db->quoteName('surv_id') . ' = ' . $db->quote($id));
		$query->setLimit(1);

		$db->setQuery($query);

		return $db->loadObject();
	}

	public static function getAnalyzeQuestion() {

		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('q.title, q.id AS qid');
		$query->from($db->quoteName('#__survey_force_analyze_quests', 'q'));
		$query->where($db->quoteName('q.state') . ' = ' . $db->quote(1));

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	// 檢查預覽議題是否已閒置過久
	public static function isSurveyExpired($_survey_id) {

		$config        = JFactory::getConfig();
		$expire_minute = $config->get('expire_minute', 30);

		$expire_time = self::getPreviewData($_survey_id, "expire_time");

		// 若未超過閒置時間，則以目前時間再往後延長
		if (time() <= $expire_time) {
			self::setPreviewData($_survey_id, "expire_time", time() + ($expire_minute * 60));

			return true;
		} else {
			return false;
		}

	}

	// 設定議題預覽Session的資料
	public static function setPreviewData($_survey_id, $_name, $_value, $_init = false) {
		$session = &JFactory::getSession();

		unset($preview_session);

		if ($_init == true) { // 初始化
			$session->clear('preview_data_' . $_survey_id); // 先清空
			$preview_session = array ();
		} else {
			$preview_session = json_decode($session->get('preview_data_' . $_survey_id), true);
		}


		$preview_session[$_name] = $_value;  // 給予變數值
		$session->Set('preview_data_' . $_survey_id, json_encode($preview_session));

		return true;

	}

	// 取回議題預覽Session的資料
	public static function getPreviewData($_survey_id, $_name) {
		$session = &JFactory::getSession();

		unset($preview_session);
		$preview_session = json_decode($session->get('preview_data_' . $_survey_id), true);

		return $preview_session[$_name];

	}

	public static function getOldArea($field, $value, $is_img = true) {
		$element = "<div class='old_" . $field . "_area'>";
		if ($is_img) {
			$element .= "<a href='../" . $value . "' class='fancybox' title='預覽檢視'>預覽檢視</a>";
		} else {
			$element .= "<a href='javascript:void(0)' class='get_pdf' id='" . $field . "' target='_blank' title='" . $value . "'>" . $value . "</a>";
		}
		$element .= "<input class='btn' type='button' id='del_" . $field . "_btn' style='width:70px ' value='刪除'>";
		$element .= "<input type='hidden' id='old_" . $field . "' name='old_" . $field . "' value='" . $value . "'>";
		$element .= "</div>";

		return $element;
	}

	public static function hiddenNewArea($selector, $element) {
		$script = $selector . '.find(".controls").find("input").hide().next("br").remove();';
		$script .= $selector . '.find(".controls").append("' . $element . '");';

		return $script;
	}

	public static function getVerifyName($verify) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$query->where($db->quoteName('access') . ' = ' . $db->quote(1));
		$query->where($db->quoteName('enabled') . ' = ' . $db->quote(1));
		$query->where($db->quoteName('folder') . ' = ' . $db->quote('verify'));
		$query->order('ordering');

		$db->setQuery($query);
		$items = $db->loadAssocList('element');

		$verify_types = json_decode($verify, true);
		unset($auths);
		$auths = array ();
		foreach ($verify_types as $verify_type) {
			$auths[] = $items[$verify_type]["name"];
		}

		return implode("、", $auths);
	}

}