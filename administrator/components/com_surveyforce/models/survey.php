<?php

/**
 * @package            Surveyforce
 * @version            1.1-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

class SurveyforceModelSurvey extends JModelAdmin {

	protected function populateState() {

		$app       = JFactory::getApplication();
		$ordering  = $app->input->get('filter_order');
		$direction = $app->input->get('filter_order_Dir');

		if ($ordering && $direction) {
			$this->setState('list.ordering', $ordering);
			$this->setState('list.direction', $direction);
		}


		$sortPublish = $app->input->getString("sortPublish");
		$this->setState('filter.publish', $sortPublish);

		$sortRequired = $app->input->getString("sortRequired");
		$this->setState('filter.required', $sortRequired);

		$search = $app->input->getString("filter_search");
		$this->setState('filter.search', $search);

		parent::populateState($ordering, $direction);
	}

	public function getTable($type = 'Survey', $prefix = 'SurveyforceTable', $config = array ()) {
		$this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array (), $loadData = true) {
		$form = $this->loadForm('com_surveyforce.survey', 'survey', array ('control' => 'jform', 'load_data' => false));
		if (empty($form)) {
			return false;
		}

		$item = $this->getItem();
		$form->bind($item);

		return $form;
	}

	public function getAllVerifyType() {
		$db = JFactory::getDBO();

		$db->setQuery("SELECT * FROM `#__extensions` WHERE `type` = 'plugin' AND `access` = '1' AND `enabled` = '1' AND `folder` = 'verify' ORDER BY `ordering`");
		$items = $db->loadObjectList();

		return $items;
	}

	// 更新欄位
	public function updateField($_field_name, $_field_value, $_id) {
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->update('#__survey_force_survs');
		$query->set($db->quoteName($_field_name) . " = " . $db->quote($_field_value));
		$query->where($db->quoteName('id') . " = " . $db->quote($_id));


		$db->setQuery($query);

		if ($db->execute()) {
			return true;
		} else {
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $query->dump()), JLog::ERROR);

			return false;
		}
	}

	// 取得議題
	public function getSurvey($_survey_id) {
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__survey_force_survs AS a');
		$query->where('a.id = ' . $db->quote($_survey_id));

		$db->setQuery($query);
		$row = $db->loadObject();

		return $row;
	}

	// 刪除議題
	public function delete($_survey_id) {
		$db = JFactory::getDbo();


		// 依序先刪選項、子選項 -> 題目 -> 議題
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__survey_force_quests');
		$query->where('sf_survey = ' . $db->quote($_survey_id));
		$db->setQuery($query);
		$question_rows = $db->loadObjectList();

		if ($question_rows) {
			foreach ($question_rows as $question_row) {
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from('#__survey_force_fields');
				$query->where('quest_id = ' . $db->quote($question_row->id));
				$db->setQuery($query);
				$option_rows = $db->loadObjectList();

				if ($option_rows) {
					foreach ($option_rows as $option_row) {
						// 刪除檔案
						if ($option_row->image) {
							unlink(JPATH_SITE . "/" . $option_row->image);
						}

						// 刪除檔案
						if ($option_row->file1) {
							unlink(JPATH_SITE . "/" . $option_row->file1);
						}
					}
				}

				// 刪除選項
				$query = $db->getQuery(true);
				$query->delete('#__survey_force_fields');
				$query->where('quest_id = ' . $db->quote($question_row->id));
				$db->setQuery($query);
				$db->execute();


				// 刪除子選項
				$query = $db->getQuery(true);
				$query->delete('#__survey_force_sub_fields');
				$query->where('quest_id = ' . $db->quote($question_row->id));
				$db->setQuery($query);
				$db->execute();
			}

			// 刪除題目資料
			$query = $db->getQuery(true);
			$query->delete('#__survey_force_quests');
			$query->where('sf_survey = ' . $db->quote($_survey_id));
			$db->setQuery($query);
			$db->execute();

			// 刪除分析欄位參數及票數統計
			$this->deleteAnalyzeColumn($_survey_id);
		}


		// 刪除議題的banner
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__survey_force_survs');
		$query->where('id = ' . $db->quote($_survey_id));
		$db->setQuery($query);
		$survey_row = $db->loadObject();

		if ($survey_row->image) {
			unlink(JPATH_SITE . "/" . $survey_row->image);
		}

		$dh   = opendir(JPATH_SITE . "/filesys/ivoting/survey/pdf/{$_survey_id}/");

		while ($file = readdir($dh)) {
			if(pathinfo($file, PATHINFO_EXTENSION) == "pdf"){
				JFile::delete(JPATH_SITE . "/filesys/ivoting/survey/pdf/{$_survey_id}/{$file}");
			}
		}

		if (in_array('assign', json_decode($survey_row->verify_type, true))) {
			$query = $db->getQuery(true);
			$query->delete('#__assign_summary');
			$query->where('survey_id = ' . $db->quote($_survey_id));
			$db->setQuery($query);
			$db->execute();
		}

		// 刪除議題資料
		$query = $db->getQuery(true);
		$query->delete('#__survey_force_survs');
		$query->where('id = ' . $db->quote($_survey_id));
		$db->setQuery($query);
		$db->execute();

		// 刪除release議題資料
		$query = $db->getQuery(true);
		$query->delete('#__survey_force_survs_release');
		$query->where('id = ' . $db->quote($_survey_id));
		$db->setQuery($query);
		$db->execute();
	}

	// 取得題目
	public function getQuestions($_survey_id) {
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__survey_force_quests AS a');
		$query->where('a.sf_survey = ' . $db->quote($_survey_id));
		$query->where('a.published = 1');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	// 取得指定題目的選項數目
	public function getOptionsCount($_question_id) {
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('COUNT(*)');
		$query->from('#__survey_force_fields AS a');
		$query->where('a.quest_id = ' . $db->quote($_question_id));

		$db->setQuery($query);
		$count = $db->loadResult();

		return $count;
	}

	// 取得單位內的所有使用者
	public function getUsersByUnit($_unit_id) {
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__users AS a');
		$query->where('a.unit_id = ' . $db->quote($_unit_id));
		$query->where('a.block = 0');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	// 取得特定使用者
	public function getUser($_user_id) {
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__users AS a');
		$query->where('a.id = ' . $db->quote($_user_id));

		$db->setQuery($query);
		$row = $db->loadObject();

		return $row;
	}

	// 更新Email通知的狀態
	public function updateEmailNotice($_survey_id, $_type) {
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->update('#__survey_force_email_notice');
		$query->set($db->quoteName('is_send') . " = '0'");
		$query->where($db->quoteName('survey_id') . " = " . $db->quote($_survey_id));


		$db->setQuery($query);

		if ($db->execute()) {
			return true;
		} else {
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $query->dump()), JLog::ERROR);

			return false;
		}
	}

	// 更新Phone通知的狀態
	public function updatePhoneNotice($_survey_id, $_type) {
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->update('#__survey_force_phone_notice');
		$query->set($db->quoteName('is_send') . " = '0'");
		$query->where($db->quoteName('survey_id') . " = " . $db->quote($_survey_id));


		$db->setQuery($query);

		if ($db->execute()) {
			return true;
		} else {
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $query->dump()), JLog::ERROR);

			return false;
		}
	}

	public function getAnalyzeColumns() {

		$id = $this->getState('survey.id');

		$db    = $this->getDBO();
		$query = $db->getQuery(true);

		$query->select('distinct(a.surv_id), a.id AS aid, a.publish, a.required, a.order, c.quest_id AS qid, c.quest_title');
		$query->from($db->quoteName('#__survey_force_analyze', 'a'));
		$query->join('LEFT', $db->quoteName('#__survey_force_analyze_count', 'c') . ' ON ' . $db->quoteName('c.quest_id') . ' = ' . $db->quoteName('a.quest_id'));
		$query->where($db->quoteName('a.surv_id') . ' = ' . $db->quote($id));
		$query->where($db->quoteName('c.survey_id') . ' = ' . $db->quote($id));

		// filter publish
		$publish = $this->getState('filter.publish');
		if (is_numeric($publish)) {
			$query->where($db->quoteName('a.publish') . ' = ' . $db->quote($publish));
		}

		// filter required
		$required = $this->getState('filter.required');
		if (is_numeric($required)) {
			$query->where($db->quoteName('a.required') . ' = ' . $db->quote($required));
		}

		// filter search
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where($db->quoteName('c.quest_title') . ' LIKE ' . $search);
		}


		$orderCol  = $this->getState('list.ordering', 'a.order');
		$orderDirn = $this->getState('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if (!$rows) {
			return null;
		} else {
			$result = [];
			foreach ($rows as $i => $row) {
				$result[$row->quest_title]['quest_id']   = $row->qid;
				$result[$row->quest_title]['analyze_id'] = $row->aid;
				$result[$row->quest_title]['surv_id']    = $row->surv_id;
				$result[$row->quest_title]['publish']    = $row->publish;
				$result[$row->quest_title]['required']   = $row->required;
			}

			return $result;
		}

	}


	// 新增分析功能的題目、選項至分析功能的票數統計資料表
	public function insertAnalyzeColumn($surv_id) {

		$db = $this->getDBO();

		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__survey_force_analyze_count'));
		$query->where($db->quoteName('survey_id') . ' = ' . $db->quote($surv_id));
		$query->Limit(1);

		$db->setQuery($query);

		if ($db->loadObject()) {
			return true;
		} else {
			$query = $db->getQuery(true);

			$query->select('a.surv_id, q.id AS qid, f.id AS fid, q.title AS quest_title, f.field_title');
			$query->from($db->quoteName('#__survey_force_analyze', 'a'));
			$query->join('LEFT', $db->quoteName('#__survey_force_analyze_quests', 'q') . ' ON ' . $db->quoteName('q.id') . ' = ' . $db->quoteName('a.quest_id'));
			$query->join('LEFT', $db->quoteName('#__survey_force_analyze_fields', 'f') . ' ON ' . $db->quoteName('f.quest_id') . ' = ' . $db->quoteName('a.quest_id'));
			$query->where($db->quoteName('a.surv_id') . ' = ' . $db->quote($surv_id));

			$db->setQuery($query);
			$rows = $db->loadObjectList();


			$created = JFactory::getDate()->toSql();

			foreach ($rows as $row) {

				try {

					$query = $db->getQuery(true);

					$db->transactionStart();

					$columns = array (
						'survey_id', 'quest_id', 'quest_title', 'field_id', 'field_title', 'count', 'created'
					);

					$values = array (
						$db->quote($surv_id), $db->quote($row->qid), $db->quote($row->quest_title), $db->quote($row->fid), $db->quote($row->field_title), $db->quote(0), $db->quote($created)
					);

					$query->insert($db->quoteName('#__survey_force_analyze_count'));
					$query->columns($db->quoteName($columns));
					$query->values(implode(',', $values));

					$db->setQuery($query);
					$db->execute();

					$db->transactionCommit();

				} catch (Exception $e) {
					// catch any database errors.
					$db->transactionRollback();
					JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $e), JLog::ERROR);

				}

			}

			return true;
		}

	}

	public function deleteAnalyzeColumn($surv_id) {

		$db = $this->getDBO();

		try {

			$query = $db->getQuery(true);

			$db->transactionStart();

			$query->delete($db->quoteName('#__survey_force_analyze'));
			$query->where($db->quoteName('surv_id') . ' = ' . $db->quote($surv_id));

			$db->setQuery($query);
			$db->execute();

			$db->transactionCommit();


		} catch (Exception $e) {
			// catch any database errors.
			$db->transactionRollback();
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $e), JLog::ERROR);
		}

		try {

			$query = $db->getQuery(true);

			$db->transactionStart();

			$query->delete($db->quoteName('#__survey_force_analyze_count'));
			$query->where($db->quoteName('survey_id') . ' = ' . $db->quote($surv_id));

			$db->setQuery($query);
			$db->execute();

			$db->transactionCommit();


		} catch (Exception $e) {
			// catch any database errors.
			$db->transactionRollback();
			JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $e), JLog::ERROR);
		}


		return true;
	}

	public function release($id) {

		$db = $this->getDBO();

		// 搜尋該議題
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__survey_force_survs'));
		$query->where($db->quoteName('id') . ' = ' . $db->quote($id));

		$db->setQuery($query);
		$items = $db->loadObject();

		// 搜尋release的議題
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__survey_force_survs_release'));
		$query->where($db->quoteName('id') . ' = ' . $db->quote($id));

		$db->setQuery($query);

		$delete = true;
		if ($db->loadObject()) { // 如果有就刪除
			try {

				$query = $db->getQuery(true);

				$db->transactionStart();

				$query->delete($db->quoteName('#__survey_force_survs_release'));
				$query->where($db->quoteName('id') . ' = ' . $db->quote($id));

				$db->setQuery($query);
				$db->execute();

				$db->transactionCommit();


			} catch (Exception $e) {
				// catch any database errors.
				$db->transactionRollback();
				JHtml::_('utility.recordLog', "db_log.php", sprintf("無法刪除：%s", $e), JLog::ERROR);
				$delete = false;
			}
		}

		// 新增至release資料表
		if ($delete) {
			return $db->insertObject('#__survey_force_survs_release', $items);
		} else {
			return false;
		}

	}

	public function checkStore($id, $stage){

		$db = $this->getDBO();
		$query = $db->getQuery(true);

		$query->select('is_store');
		$query->from($db->quoteName('#__survey_force_survs'));
		$query->where($db->quoteName('id') . ' = ' . $db->quote($id));

		$db->setQuery($query);
		$item = $db->loadObject();

		$is_store = json_decode($item->is_store, true);

		return json_encode($is_store[$stage]);

	}

}
