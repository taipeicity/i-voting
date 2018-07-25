<?php

/**
 * @package            Surveyforce
 * @version            1.1-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die('Restricted access');
define('DS', DIRECTORY_SEPARATOR);

class SurveyforceControllerSurvey extends JControllerForm {

	protected $last_insert_id;

	public function __construct() {
		$this->_trackAssets = true;
		parent::__construct();
	}

	public function cancel() {
		// 清掉階段的session
		$input = JFactory::getApplication()->input;
		$pk    = $input->get('id') ? $input->get('id') : 'new';

		$session = JFactory::getSession();
		$session->clear('edit_stage.' . $pk);
		$session->clear('stage');
		$session->clear('mark');

		$this->setRedirect('index.php?option=com_surveyforce&view=surveys');
	}

	protected function postSaveHook(JModelLegacy $model, $validData = array ()) {
		jimport('joomla.filesystem.folder');
		$app    = JFactory::getApplication();
		$config = JFactory::getConfig();

		$ivoting_path      = $config->get('ivoting_path');
		$ivoting_save_path = $config->get('ivoting_save_path');
		$tmp_path          = $config->get('tmp_path');

		$this->last_insert_id = $model->getState($model->getName() . '.id');

		$post = $app->input->getArray($_POST);

		if ($app->input->get('task') == "apply" || $app->input->get('task') == "save") {

			// 上傳檔案
			if ($ivoting_path) {
				$upload_files = $app->input->files->get("jform");

				foreach ($upload_files as $field => $uploadFile) {
					if ($field == "place_image") {
						if ($validData["is_place"] != 1) {
							continue;
						}
					}

					if (is_array($uploadFile) && $uploadFile["name"]) {

						$result["msg"] = "";

						if ($uploadFile["error"] != 0) {
							$result["msg"] = "上傳檔案失敗。";
						}

						$type = strpos($uploadFile["type"], "image") === false ? "pdf" : "image";

						switch ($type) {
							case "image":
								$mb         = 2;
								$allow_type = array ("image/jpeg", "image/pjpeg", "image/png", "image/gif");
								$allow      = "jpg/png/gif";
								break;
							case"pdf":
								$mb         = 5;
								$allow_type = array ("application/pdf");
								$allow      = "pdf";
								break;
						}

						// 檢查檔案大小
						if ($uploadFile["size"] > 1048576 * $mb) {
							$result["msg"] = "上傳檔超過指定大小({$mb}MB)。";
						}

						// 檢查副檔名
						if (!in_array($uploadFile["type"], $allow_type)) {
							$result["msg"] = "只允許上傳檔案類型：{$allow}。";
						}

						// 上傳
						if ($result["msg"] == "") {
							if ($type == "image") {
								// 非JPG檔做轉換
								if (exif_imagetype($uploadFile['tmp_name']) != 2) {
									JHtml::_('utility.transformImg', $uploadFile['tmp_name'], $uploadFile['tmp_name'], "jpeg");
								}

								// 壓縮圖片
								JHtml::_('utility.compressImg', $tmp_path, $uploadFile['tmp_name'], 80);

								$file      = $ivoting_path . "/survey/surveys/" . $this->last_insert_id . "_image.jpg";
								$desc_file = JPATH_SITE . "/" . $file;
								JFile::upload($uploadFile['tmp_name'], $desc_file);
							} else {
								// 上傳pdf
								$desc_pdf = JPATH_SITE . DS . $ivoting_path . "/survey/pdf/" . $this->last_insert_id . DS . $field . ".pdf";
								$file     = $uploadFile["name"];

								// 檢查路徑權限，非755就修改
								$check_permissions = substr(sprintf('%o', fileperms(JPATH_SITE . $ivoting_path . "/survey/pdf/" . $this->last_insert_id)), -4);
								if ($check_permissions != "0755") {
									chmod(JPATH_SITE . DS . $ivoting_path . "/survey/pdf/" . $this->last_insert_id, 0755);
								}
								if (!JFile::upload($uploadFile["tmp_name"], $desc_pdf)) {
									JError::raiseWarning(100, "上傳檔案失敗。");
								}
							}
						} else {
							JError::raiseWarning(100, $result["msg"]);
						}
					} else {
						$old_file = $app->input->getString('old_' . $field);
						if ($old_file) {
							$file = $old_file;
						} else {
							if (preg_match("/image/", $field)) {
								if ($field == "place_image") {
									$default_img = "images/system/idnum_sample.jpg";
									$dest_img = $ivoting_path . "/survey/surveys/" . $this->last_insert_id . "_place_image.jpg";
								} else {
									$default_img = "images/system/banner_default.jpg";
									$dest_img = $ivoting_path . "/survey/surveys/" . $this->last_insert_id . "_image.jpg";
								}

								if (JFile::copy($default_img, $dest_img, JPATH_SITE)) {
									$file = $dest_img;
								} else {
									$file = $default_img;
								}
							} else {
								$old_pdf = $ivoting_path . "/survey/pdf/" . $this->last_insert_id . DS . $field . ".pdf";
								if (JFile::exists(JPATH_SITE . DS . $old_pdf)) {
									JFile::delete(JPATH_SITE . DS . $old_pdf);
								}
								$file = $old_file;
							}
						}
					}
					$model->updateField($field, $file, $this->last_insert_id);
				}
			} else {
				JError::raiseWarning(100, '存檔路徑尚未設置，請通知系統管理員。');
			}

			// 驗證後置處理
			switch ($post["verify_method"]) {
				case 0:
					break;
				case 1:  // 依強度選擇驗證方式
					// 載入plugin
					JPluginHelper::importPlugin('verify', $post["verify_mix"]);
					$className = 'plgVerify' . ucfirst($post["verify_mix"]);

					// 後置處理
					if (method_exists($className, 'onAdminSaveHook')) {
						$className::onAdminSaveHook($post, $this->last_insert_id);
					}

					break;
				case 2:  // 自訂驗證
					foreach ($post["verify_custom"] as $verify) {
						// 載入plugin
						JPluginHelper::importPlugin('verify', $verify);
						$className = 'plgVerify' . ucfirst($verify);

						// 後置處理
						if (method_exists($className, 'onAdminSaveHook')) {
							$className::onAdminSaveHook($post, $this->last_insert_id);
						}
					}

					break;
				default:
					break;
			}


			// 新增資料夾
			if (!JFolder::exists($ivoting_save_path . "/ca/" . $this->last_insert_id)) {
				JFolder::create($ivoting_save_path . "/ca/" . $this->last_insert_id, 0755);
			}


			// 若修改投票時間，則一併修改寄送通知
			$now = JFactory::getDate()->toSql();
			if ($validData['vote_start'] >= $now) {
				$model->updateEmailNotice($this->last_insert_id, 1);
				$model->updatePhoneNotice($this->last_insert_id, 1);
			}


			if ($validData['vote_end'] >= $now) {
				$model->updateEmailNotice($this->last_insert_id, 2);
				$model->updateEmailNotice($this->last_insert_id, 3);
				$model->updatePhoneNotice($this->last_insert_id, 2);
				$model->updatePhoneNotice($this->last_insert_id, 3);
			}

			if ((SurveyforceHelper::checkAnalyze($this->last_insert_id) == null ? true : false) && $post["jform"]["is_analyze"] == 1) {

				$items = SurveyforceHelper::getAnalyzeQuestion();
				$i     = 0;

				$forms = [];
				foreach ($items as $item) {
					$forms[$i]['id']       = '';
					$forms[$i]['surv_id']  = $this->last_insert_id;
					$forms[$i]['quest_id'] = $item->qid;
					$forms[$i]['publish']  = 0;
					$forms[$i]['required'] = 0;
					$forms[$i]['order']    = $i;
					$i++;
				}

				$analyze_param = $model->getTable('Analyze_param');

				//新增分析功能的題目參數資料
				foreach ($forms as $form) {
					$analyze_param->bind($form);
					$analyze_param->save($form);
				}

				//新增分析功能的票數統計資料
				$model->insertAnalyzeColumn($this->last_insert_id);
			}

			// 關閉分析功能即刪除該議題分析資料
			if ($post["jform"]["is_analyze"] == 0) {

				$db    = JFactory::getDBO();
				$query = $db->getQuery(true);

				$query->select('*');
				$query->from($db->quoteName('#__survey_force_analyze'));
				$query->where($db->quoteName('surv_id') . ' = ' . $db->quote($this->last_insert_id));
				$query->Limit(1);

				$db->setQuery($query);

				if ($db->loadObject()) {
					$model->deleteAnalyzeColumn($this->last_insert_id);
				}

			}

		}

		if ($app->input->get('task') == 'save2copy') { //複製議題欄位 voters_authentication、verify_type、verify_params

			$prefix_words = JComponentHelper::getParams('com_surveyforce')->get('prefix_words');
			$survs        = $model->getSurvey($app->input->getInt('id'));
			$user         = JFactory::getUser();

			$db = JFactory::getDbo();

			$object = new stdClass();

			$survs->id    = $this->last_insert_id;
			$survs->title = $prefix_words . $survs->title;
			$old_image    = $survs->image;
			$survs->image = str_replace($app->input->getInt('id'), $this->last_insert_id, $survs->image);

			if ($survs->place_image) {
				$old_place_image    = $survs->place_image;
				$survs->place_image = str_replace($app->input->getInt('id'), $this->last_insert_id, $survs->place_image);
			}

			$survs->published   = 0;
			$survs->is_checked  = 0;
			$survs->is_complete = 0;
			$survs->created_by  = $user->get('id');
			$survs->checked_by  = 0;
			$survs->total_vote  = 0;

			$survs->stage = 1;

			$is_store = json_decode($survs->is_store, true);
			foreach ($is_store as $num => $status) {
				$is_store[$num] = false;
			}
			$survs->is_store = json_encode($is_store);

			$verify_params = json_decode($survs->verify_params, true);
			$now_time      = date("YmdHis", time());  // 取現在的時間當做table後綴字

			//複製可投票人名單總覽
			if (in_array('assign', json_decode($survs->verify_type, true))) {

				$query = $db->getQuery(true);

				$query->select('survey_id, table_suffix, column_num, title, note');
				$query->from($db->quoteName('#__assign_summary'));
				$query->where($db->quoteName('table_suffix') . ' = ' . $db->quote($verify_params['assign']['assign_table_suffix']));
				$query->where($db->quoteName('survey_id') . ' = ' . $db->quote($app->input->getInt('id')));
				$query->order($db->quoteName('column_num') . ' ASC');

				$db->setQuery($query);
				$assign_summarys = $db->loadObjectList();

				$assign_table_suffix = $verify_params['assign']['assign_table_suffix'];

				// 至Agent複製可投票人驗證名單
				if (!$this->Save2Copy('assign', $now_time, $assign_table_suffix, count($assign_summarys))) {
					$app->enqueueMessage(JText::_('可投票人名單總覽複製失敗'), 'error');
				}
				$verify_params['assign']['assign_table_suffix'] = $now_time; // 處理新的後綴字


				if ($assign_summarys) {
					foreach ($assign_summarys as $assign_summary) {
						$assign_summary->survey_id    = $this->last_insert_id;
						$assign_summary->table_suffix = $now_time;

						try {

							$db->transactionStart();

							$db->insertObject('#__assign_summary', $assign_summary);

							$db->transactionCommit();

						} catch (Exception $e) {

							// catch any database errors.
							$db->transactionRollback();
							JHtml::_('utility.recordLog', "db_log.php", sprintf("無法新增可投票人名單總覽：%s", $e), JLog::ERROR);
							$app->enqueueMessage(JText::_('可投票人名單總覽複製失敗'), 'error');
						}
					}
				}
			}

			if (in_array('idnum', json_decode($survs->verify_type, true))) {
				$idnum_table_suffix = $verify_params['idnum']['idnum_table_suffix'];

				// 至Agent複製身分證驗證名單
				if (!$this->Save2Copy('idnum', $now_time, $idnum_table_suffix)) {
					$app->enqueueMessage(JText::_('身分證名單總覽複製失敗'), 'error');
				}
				$verify_params['idnum']['idnum_table_suffix'] = $now_time; // 處理新的後綴字

			}

			if (in_array('any', json_decode($survs->verify_type, true)) && array_key_exists('suffix', $verify_params['any'])) {
				$school_table_suffix = $verify_params['any']['suffix'];
				$this->Save2Copy('any', $now_time, $school_table_suffix); // 至Agent複製身分證驗證名單
				$verify_params['any']['suffix'] = $now_time; // 處理新的後綴字
			}

			$survs->verify_params = json_encode($verify_params);

			try {

				$db->transactionStart();

				$db->updateObject('#__survey_force_survs', $survs, 'id');

				$db->transactionCommit();

			} catch (Exception $e) {

				// catch any database errors.
				$db->transactionRollback();
				JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $e), JLog::ERROR);

			}

			// 複製圖片
			JFile::copy($old_image, $survs->image, JPATH_SITE);
			// 複製現地圖片
			if ($survs->place_image) {
				JFile::copy($old_place_image, $survs->place_image, JPATH_SITE);
			}

			//檢查路徑
			if (!JFolder::exists(JPATH_SITE . '/' . $ivoting_path . '/survey/pdf/' . $this->last_insert_id)) {
				JFolder::create(JPATH_SITE . '/' . $ivoting_path . '/survey/pdf/' . $this->last_insert_id, 0755);
			}

			$dh = opendir(JPATH_SITE . "/filesys/ivoting/survey/pdf/{$app->input->getInt('id')}/");

			while ($file = readdir($dh)) {
				if (pathinfo($file, PATHINFO_EXTENSION) == "pdf") {
					$src  = $ivoting_path . '/survey/pdf/' . $app->input->getInt('id') . '/' . $file;
					$dest = $ivoting_path . '/survey/pdf/' . $this->last_insert_id . '/' . $file;
					JFile::copy($src, $dest, JPATH_SITE);
				}
			}


			// 複製題目
			$query = $db->getQuery(true);

			$query->select('*');
			$query->from($db->quoteName('#__survey_force_quests'));
			$query->where($db->quoteName('sf_survey') . ' = ' . $db->quote($app->input->getInt('id')));
			$query->order($db->quoteName('id') . ' ASC');

			$db->setQuery($query);
			$quests = $db->loadObjectList();

			if ($quests) {
				foreach ($quests as $quest) {
					$quest_id = $quest->id;
					unset($quest->id);
					$quest->sf_survey = $this->last_insert_id;

					try {

						$db->transactionStart();

						$db->insertObject('#__survey_force_quests', $quest); // 新增複製的題目

						$new_quest_id = $db->insertid();

						$db->transactionCommit();

					} catch (Exception $e) {

						// catch any database errors.
						$db->transactionRollback();
						JHtml::_('utility.recordLog', "db_log.php", sprintf("無法新增議題題目：%s", $e), JLog::ERROR);
						$app->enqueueMessage(JText::_('議題題目複製失敗'), 'error');

					}

					if ($quest->question_type == 'imgcat') {

						// 複製選項分類
						$query = $db->getQuery(true);
						$query->select('*');
						$query->from($db->quoteName('#__survey_force_quests_cat'));
						$query->where($db->quoteName('question_id') . ' = ' . $db->quote($quest_id));
						$query->order($db->quoteName('id') . ' ASC');

						$db->setQuery($query);
						$cats = $db->loadObjectList();

						if ($cats) {
							foreach ($cats as $cat) {
								$catid = $cat->id;
								unset($cat->id);
								$cat->question_id = $new_quest_id;

								try {

									$db->transactionStart();

									$db->insertObject('#__survey_force_quests_cat', $cat); // 新增複製的分類

									$sort_catid[$catid] = $db->insertid();

									$db->transactionCommit();

								} catch (Exception $e) {

									// catch any database errors.
									$db->transactionRollback();
									JHtml::_('utility.recordLog', "db_log.php", sprintf("無法新增議題題目分類：%s", $e), JLog::ERROR);
									$app->enqueueMessage(JText::_('議題題目複製失敗'), 'error');

								}
							}
						}
					}

					// 複製選項
					$query = $db->getQuery(true);

					$query->select('*');
					$query->from($db->quoteName('#__survey_force_fields'));
					$query->where($db->quoteName('quest_id') . ' = ' . $db->quote($quest_id));
					$query->order($db->quoteName('id') . ' ASC');

					$db->setQuery($query);
					$fields = $db->loadObjectList();

					foreach ($fields as $key => $field) {
						$field->quest_id = $new_quest_id; // 將quest_id設成新的
						$field_id        = $field->id; // 將原本的id存起來
						unset($field->id);

						try {

							$db->transactionStart();

							$db->insertObject('#__survey_force_fields', $field); // 新增複製的選項

							$new_field_id = $db->insertid(); // 取得剛才新增資料的id

							$db->transactionCommit();

						} catch (Exception $e) {

							// catch any database errors.
							$db->transactionRollback();
							JHtml::_('utility.recordLog', "db_log.php", sprintf("議題選項：%s", $e), JLog::ERROR);
							$app->enqueueMessage(JText::_('議題選項複製失敗'), 'error');

						}

						// 如果複製的選項有圖片或是附加檔
						if ($field->image || $field->file1 || $quest->question_type == 'imgcat') {
							$update_field     = new stdClass();
							$update_field->id = $new_field_id;
							if ($field->image) {
								$update_field->image = str_replace($field_id, $new_field_id, $field->image); // 替換圖片路徑
								JFile::copy($field->image, $update_field->image, JPATH_SITE); // 複製到新路徑
								if ($quest->question_type == 'imgcat') {
									JFile::copy($field->image, str_replace("_image_", "_image_s", $update_field->image), JPATH_SITE); // 複製到新路徑
									$update_field->catid = $sort_catid[(int) $field->catid];
								}
							}

							if ($field->file1 && $quest->question_type != 'video') {
								$update_field->file1 = str_replace($field_id, $new_field_id, $field->file1); // 替換附加檔路徑
								JFile::copy($field->file1, $update_field->file1, JPATH_SITE); // 複製到新路徑
							}

							try {

								$db->transactionStart();

								$db->updateObject('#__survey_force_fields', $update_field, 'id');

								$db->transactionCommit();

							} catch (Exception $e) {

								// catch any database errors.
								$db->transactionRollback();
								JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新有圖片或附加檔的選項：%s", $e), JLog::ERROR);
								$app->enqueueMessage(JText::_('議題選項複製失敗'), 'error');

							}

						}
					}

					// 複製子選項
					$query = $db->getQuery(true);

					$query->select('quest_id, title, ordering');
					$query->from($db->quoteName('#__survey_force_sub_fields'));
					$query->where($db->quoteName('quest_id') . ' = ' . $db->quote($quest_id));
					$query->order($db->quoteName('id') . ' ASC');

					$db->setQuery($query);
					$sub_fields = $db->loadObjectList();

					if ($sub_fields) {
						foreach ($sub_fields as $sub_field) {
							$sub_field->quest_id = $new_quest_id;

							try {

								$db->transactionStart();

								$db->insertObject('#__survey_force_sub_fields', $sub_field); // 新增複製的子選項

								$db->transactionCommit();

							} catch (Exception $e) {

								// catch any database errors.
								$db->transactionRollback();
								JHtml::_('utility.recordLog', "db_log.php", sprintf("無法新增議題子選項：%s", $e), JLog::ERROR);
								$app->enqueueMessage(JText::_('議題子選項複製失敗'), 'error');

							}
						}
					}
				}
			}


			// 複製分析功能
			if ($survs->is_analyze == 1) {

				// 複製設定參數欄位
				$query = $db->getQuery(true);

				$query->select('*');
				$query->from($db->quoteName('#__survey_force_analyze'));
				$query->where($db->quoteName('surv_id') . ' = ' . $db->quote($app->input->getInt('id')));
				$query->order($db->quoteName('id'));

				$db->setQuery($query);
				$rows = $db->loadObjectList();

				foreach ($rows as $row) {
					unset($row->id);
					$row->surv_id = $this->last_insert_id;

					try {

						$db->transactionStart();

						$db->insertObject('#__survey_force_analyze', $row);

						$db->transactionCommit();

					} catch (Exception $e) {

						// catch any database errors.
						$db->transactionRollback();
						JHtml::_('utility.recordLog', "db_log.php", sprintf("無法新增分析功能參數：%s", $e), JLog::ERROR);
						$app->enqueueMessage(JText::_('議題分析功能參數複製失敗'), 'error');

					}

				}

				// 複製分析資料票數統計欄位
				$query = $db->getQuery(true);

				$query->select('*');
				$query->from($db->quoteName('#__survey_force_analyze_count'));
				$query->where($db->quoteName('survey_id') . ' = ' . $db->quote($app->input->getInt('id')));

				$db->setQuery($query);
				$rows = $db->loadObjectList();

				foreach ($rows as $row) {
					unset($row->id);
					$row->survey_id = $this->last_insert_id;
					$row->created   = JFactory::getDate()->format('Y-m-d h:i:s');
					$row->count     = 0;

					try {

						$db->transactionStart();

						$db->insertObject('#__survey_force_analyze_count', $row);

						$db->transactionCommit();

					} catch (Exception $e) {

						// catch any database errors.
						$db->transactionRollback();
						JHtml::_('utility.recordLog', "db_log.php", sprintf("無法新增分析功能票數欄位統計：%s", $e), JLog::ERROR);
						$app->enqueueMessage(JText::_('議題分析功能票數統計欄位複製失敗'), 'error');

					}

				}
			}
		}
	}


	public function save() {
		$task = JFactory::getApplication()->input->get('task');
		$save = parent::save();
	}

	// 刪除
	public function delete() {
		$model     = $this->getModel();
		$app       = JFactory::getApplication();
		$jinput    = $app->input;
		$jform     = $app->input->get('jform', '', 'array');
		$survey_id = $jform['id'];

		$date    = JFactory::getDate();
		$nowDate = $date->toSql();

		$user    = JFactory::getUser();
		$user_id = $user->get('id');
		$unit_id = $user->get('unit_id');

		$survey = $model->getSurvey($survey_id);

		$created_user    = JFactory::getUser($survey->created_by);
		$created_unit_id = $created_user->get('unit_id');

		$state = $this->get('State');
		$canDo = JHelperContent::getActions('com_surveyforce');


		$self_gps    = JUserHelper::getUserGroups($user->get('id'));
		$core_review = JComponentHelper::getParams('com_surveyforce')->get('core_review');

		// 作者 或 同單位審核者 或 最高權限 才可儲存和刪除
		if ($survey->created_by == $user_id || ($unit_id == $created_unit_id && in_array($core_review, $self_gps)) || $canDo->get('core.own')) {
			// 是否為已投票
			if ($survey->complete && $survey->checked && (strtotime($survey->vote_start) < strtotime($nowDate)) && (strtotime($survey->vote_end) > strtotime($nowDate))) {
				JError::raiseWarning(100, '該議題正在進行投票中，無法進行刪除。');
			} else {
				$model->delete($survey_id);
				JError::raiseNotice(100, '議題刪除成功。');
			}
		} else {
			JError::raiseWarning(100, '權限不足，該議題無法進行刪除。');
		}


		$this->cancel();
	}

	// 送審
	public function send_check() {
		$model  = $this->getModel();
		$config = JFactory::getConfig();
		$app    = JFactory::getApplication();
		$jform  = $app->input->get('jform', '', 'array');

		$user    = JFactory::getUser();
		$unit_id = $user->get('unit_id');

		$check = true;

		$stage = [
			1 => JText::_("COM_SURVEYFORCE_CHECK"), 2 => JText::_("COM_SURVEYFORCE_REVIEW"), 3 => JText::_("COM_SURVEYFORCE_DISCUSS"), 4 => JText::_("COM_SURVEYFORCE_OPTIONS"), 5 => JText::_("COM_SURVEYFORCE_LAUNCHED"), 6 => JText::_("COM_SURVEYFORCE_RESULT")
		];


		if (!$model->checkStore($jform['id'], $jform['stage'])) {
			$check = false;
			JError::raiseNotice(100, JText::sprintf('COM_SURVEYFORCE_SEND_CHECK', $stage[$jform['stage']]));
		}

		if ($jform["stage"] > 4) {
			// 檢查是否有新增題目
			$questions = $model->getQuestions($jform['id']);

			if ($questions) {
				//  檢查每個題目是否都有選項
				unset($mesg);
				$mesg = array ();
				foreach ($questions as $question) {
					if ($model->getOptionsCount($question->id) <= 0) {
						$mesg[] = "題目 - " . $question->sf_qtext . " 尚未新增選項";
					}
				}

				if (count($mesg) > 0) {
					$check = false;
					JError::raiseWarning(100, implode("<br>", $mesg));
				}
			} else {
				$check = false;
				JError::raiseWarning(100, '該議題未設定題目，請先新增題目。');
			}
		}

		if ($check) {
			// 更新欄位
			$model->updateField("is_complete", 1, $jform['id']);
			$model->updateField("is_checked", 0, $jform['id']);
			$model->updateField("published", 0, $jform['id']);

			// 寄發Email郵件通知審核人員
			$users = $model->getUsersByUnit($unit_id);
			foreach ($users as $user) {
				$groups = JAccess::getGroupsByUser($user->id, false);
				if (in_array(4, $groups) && $user->email) {

					$sitename   = $config->get('sitename');
					$from_email = $config->get('mailfrom');
					$from_name  = $config->get('fromname');

					$subject   = $sitename . "-議題審核通知";
					$alert_msg = "<p>您好：<br><br>";
					$alert_msg .= "新投票議題：「" . $jform['title'] . "」<br>";
					$alert_msg .= "請盡速登入系統後台進行議題審核。<br><br>";
					$alert_msg .= "" . $sitename . " 敬上<br><br>";
					$alert_msg .= "◎備註：此信件由系統自動發出，請不要回覆。</p>";

					$send_email_status = JHtml::_('utility.sendMail', $from_email, $from_name, $user->email, $subject, $alert_msg, 1);


					if (is_object($send_email_status)) {
						$send_email_status = 0;
						JHtml::_('utility.recordLog', "debug_log.php", "議題ID:" . $jform['id'] . ",審核通知無法發送", JLog::ERROR);
					}

					$encode_email = JHtml::_('utility.endcode', $user->email);
					JHtml::_('utility.sendMailRecord', $send_email_status, $from_email, $from_name, $encode_email, $subject, $alert_msg, 1);
				}
			}

			JError::raiseNotice(100, '議題送審成功，將由審核人員進行審核');
		}


		$this->cancel();
	}

	// 審核通過
	public function pass_success() {
		$model  = $this->getModel();
		$config = JFactory::getConfig();
		$app    = JFactory::getApplication();

		$jform = $app->input->get('jform', '', 'array');

		$check = true;

		$stage = [
			1 => JText::_("COM_SURVEYFORCE_CHECK"), 2 => JText::_("COM_SURVEYFORCE_REVIEW"), 3 => JText::_("COM_SURVEYFORCE_DISCUSS"), 4 => JText::_("COM_SURVEYFORCE_OPTIONS"), 5 => JText::_("COM_SURVEYFORCE_LAUNCHED"), 6 => JText::_("COM_SURVEYFORCE_RESULT")
		];


		if (!$model->checkStore($jform['id'], $jform['stage'])) {
			$check = false;
			JError::raiseNotice(100, JText::sprintf('COM_SURVEYFORCE_SEND_CHECK', $stage[$jform['stage']]));
		}

		if ($jform["stage"] > 4) {

			// 檢查是否有新增題目
			$questions = $model->getQuestions($jform['id']);

			if ($questions) {
				//  檢查每個題目是否都有選項
				unset($mesg);
				$mesg = array ();
				foreach ($questions as $question) {
					if ($model->getOptionsCount($question->id) <= 0) {
						$mesg[] = "題目 - " . $question->sf_qtext . " 尚未新增選項";
					}
				}

				if (count($mesg) > 0) {
					$check = false;
					JError::raiseWarning(100, implode("<br>", $mesg));
				}

			} else {
				$check = false;
				JError::raiseWarning(100, '該議題未設定題目，請確認是否已新增題目。');
			}
		}

		if ($check) {
			$date    = JFactory::getDate();
			$nowDate = $date->toSql();
			$user    = JFactory::getUser();
			$user_id = $user->get('id');

			// 更新欄位
			$model->updateField("is_complete", 1, $jform['id']);
			$model->updateField("is_checked", 1, $jform['id']);
			$model->updateField("published", 1, $jform['id']);
			$model->updateField("checked", $nowDate, $jform['id']);
			$model->updateField("checked_by", $user_id, $jform['id']);

			// 將資料倒進release
			$model->release($jform['id']);

			// 寄發Email郵件通知承辦人員
			$user = JFactory::getUser($jform['created_by']);


			$sitename   = $config->get('sitename');
			$from_email = $config->get('mailfrom');
			$from_name  = $config->get('fromname');

			$subject   = $sitename . "-議題審核通過通知";
			$alert_msg = "<p>您好：<br><br>";
			$alert_msg .= "投票議題：「" . $jform['title'] . "」<br>";
			$alert_msg .= "該議題已審核通過。<br><br>";
			$alert_msg .= "" . $sitename . " 敬上<br><br>";
			$alert_msg .= "◎備註：此信件由系統自動發出，請不要回覆。</p>";

			$send_email_status = JHtml::_('utility.sendMail', $from_email, $from_name, $user->email, $subject, $alert_msg, 1);


			if (is_object($send_email_status)) {
				$send_email_status = 0;
				JHtml::_('utility.recordLog', "debug_log.php", "議題ID:" . $jform['id'] . ",審核過通無法發送", JLog::ERROR);
			}

			$encode_email = JHtml::_('utility.endcode', $user->email);
			JHtml::_('utility.sendMailRecord', $send_email_status, $from_email, $from_name, $encode_email, $subject, $alert_msg, 1);


			JError::raiseNotice(100, '議題審核通過成功');
		}


		$this->cancel();
	}

	// 審核不通過
	public function pass_fail() {
		$model  = $this->getModel();
		$config = JFactory::getConfig();
		$app    = JFactory::getApplication();
		$jinput = $app->input;
		$jform  = $app->input->get('jform', '', 'array');

		$date    = JFactory::getDate();
		$nowDate = $date->toSql();
		$user    = JFactory::getUser();
		$user_id = $user->get('id');


		// 更新欄位
		$model->updateField("is_complete", 0, $jform['id']);
		$model->updateField("is_checked", 0, $jform['id']);
		$model->updateField("published", 0, $jform['id']);
		$model->updateField("checked", $nowDate, $jform['id']);
		$model->updateField("checked_by", $user_id, $jform['id']);

		// 寄發Email郵件通知承辦人員
		$user = JFactory::getUser($jform['created_by']);


		$sitename   = $config->get('sitename');
		$from_email = $config->get('mailfrom');
		$from_name  = $config->get('fromname');

		$subject   = $sitename . "-議題審核不通過通知";
		$alert_msg = "<p>您好：<br><br>";
		$alert_msg .= "投票議題：「" . $jform['title'] . "」<br>";
		$alert_msg .= "該議題審核為不通過，原因如下：";
		$alert_msg .= $jform['fail_reason'] . "<br><br>";
		$alert_msg .= "" . $sitename . " 敬上<br><br>";
		$alert_msg .= "◎備註：此信件由系統自動發出，請不要回覆。</p>";

		$send_email_status = JHtml::_('utility.sendMail', $from_email, $from_name, $user->get('email'), $subject, $alert_msg, 1);


		if (is_object($send_email_status)) {
			$send_email_status = 0;
			JHtml::_('utility.recordLog', "debug_log.php", "議題ID:" . $jform['id'] . ",審核不通過無法發送", JLog::ERROR);
		}

		$encode_email = JHtml::_('utility.endcode', $user->get('email'));
		JHtml::_('utility.sendMailRecord', $send_email_status, $from_email, $from_name, $encode_email, $subject, $alert_msg, 1);


		JError::raiseNotice(100, '議題設為審核不通過，已通知議題承辦人員。');


		$this->cancel();
	}

	// 重新審核
	public function recheck() {
		$model  = $this->getModel();
		$config = JFactory::getConfig();
		$app    = JFactory::getApplication();
		$jinput = $app->input;
		$jform  = $app->input->get('jform', '', 'array');

		$user    = JFactory::getUser();
		$unit_id = $user->get('unit_id');


		// 更新欄位
		$model->updateField("is_complete", 1, $jform['id']);
		$model->updateField("is_checked", 0, $jform['id']);
		$model->updateField("published", 0, $jform['id']);

		// 寄發Email郵件通知審核人員
		$users = $model->getUsersByUnit($unit_id);
		foreach ($users as $user) {
			$groups = JAccess::getGroupsByUser($user->id, false);
			if (in_array(4, $groups) && $user->email) {  // 若為審核者群組 4，則寄發Email通知
				$sitename   = $config->get('sitename');
				$from_email = $config->get('mailfrom');
				$from_name  = $config->get('fromname');

				$subject   = $sitename . "-議題重新審核通知";
				$alert_msg = "<p>您好：<br><br>";
				$alert_msg .= "投票議題：「" . $jform['title'] . "」<br>";
				$alert_msg .= "請盡速登入系統後台進行議題重新審核。<br><br>";
				$alert_msg .= "" . $sitename . " 敬上<br><br>";
				$alert_msg .= "◎備註：此信件由系統自動發出，請不要回覆。</p>";

				$send_email_status = JHtml::_('utility.sendMail', $from_email, $from_name, $user->email, $subject, $alert_msg, 1);


				if (is_object($send_email_status)) {
					$send_email_status = 0;
					JHtml::_('utility.recordLog', "debug_log.php", "議題ID:" . $jform['id'] . ",重新審核通知無法發送", JLog::ERROR);
				}

				$encode_email = JHtml::_('utility.endcode', $user->email);
				JHtml::_('utility.sendMailRecord', $send_email_status, $from_email, $from_name, $encode_email, $subject, $alert_msg, 1);
			}
		}

		JError::raiseNotice(100, '議題修改成功，將由審核人員重新進行審核');


		$this->cancel();
	}

	public function analyze() {
		$app    = JFactory::getApplication();
		$action = $app->input->getString("action");

		if ($action == "publish" || $action == "required") {

			$id = $app->input->getInt("analyze_id");

			$object     = new stdClass();
			$object->id = $id;

			switch ($action) {
				case "publish" :
					$object->publish = ($app->input->getInt("publish") == 0) ? 1 : 0;
					break;
				case "required" :
					$object->required = ($app->input->getInt("required") == 0) ? 1 : 0;
					break;
			}


			$db = JFactory::getDbo();

			try {

				$db->transactionStart();

				$result = $db->updateObject('#__survey_force_analyze', $object, 'id');

				$db->transactionCommit();

			} catch (Exception $e) {

				// catch any database errors.
				$db->transactionRollback();
				JHtml::_('utility.recordLog', "db_log.php", sprintf("無法更新：%s", $e), JLog::ERROR);

			}

		} else {

			$id       = $app->input->getInt("id");
			$quest_id = $app->input->getInt("quest_id");

			$db    = JFactory::getDBO();
			$query = $db->getQuery(true);

			$query->select('*');
			$query->from($db->quoteName('#__survey_force_analyze_count'));
			$query->where($db->quoteName('survey_id') . ' = ' . $db->quote($id));
			$query->where($db->quoteName('quest_id') . ' = ' . $db->quote($quest_id));
			$query->order($db->quoteName('field_id'));

			$db->setQuery($query);

			echo json_encode($db->loadObjectList());
		}
		exit;
	}

	public function getPdf() {

		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app    = JFactory::getApplication();
		$config = JFactory::getConfig();

		$original_name = $app->input->getString('original_name');
		$survey_id     = $app->input->getInt('id');
		$file_name     = $app->input->getString('file_name');
		$path          = JPATH_SITE . '/' . $config->get('ivoting_path') . '/survey/pdf/' . $survey_id . '/' . $file_name . '.pdf';

		header('Cache-Control: public, must-revalidate');
		header('Content-Type: application/octet-stream');
		header('Content-Length: ' . (string) (filesize($path)));
		header('Content-Disposition: attachment; filename="' . $original_name . '"');
		readfile($path);

		exit;

	}

	public function Save2Copy($verify_type, $now_time, $suffix, $count = 0) {  // 至Agent複製可投票人驗證名單或身分證驗證名單

		$plugin          = JPluginHelper::getPlugin('verify', $verify_type);
		$pluginParams    = new JRegistry($plugin->params);
		$api_request_url = $pluginParams->get('api_url_copy');


		unset($api_request_parameters);

		$api_request_parameters = array (
			'new_suffix' => $now_time, 'old_suffix' => $suffix, 'verify_type' => $verify_type
		);

		if ($count > 0) {
			$api_request_parameters['count'] = (int) $count; // 可投票人名單的欄位個數
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $api_request_parameters);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Accept: application/json'));
		curl_setopt($ch, CURLOPT_URL, $api_request_url);
		$api_response = curl_exec($ch);
		$code         = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$message      = curl_error($ch);

		curl_close($ch);

		if ($code == 200) {
			$response = json_decode($api_response);

			if ($response->status == 1) {
				return true;
			} else {
				$result["msg"] = $response->msg;
				// 記錄log
				JHtml::_('utility.recordLog', "api_log.php", sprintf("Url:%s, Code:%d, Msg:%s", $api_request_url, $code, $result["msg"]), JLog::ERROR);

				return false;
			}
		} else {
			// 記錄log
			JHtml::_('utility.recordLog', "api_log.php", sprintf("Url:%s, Code:%d, Msg:%s", $api_request_url, $code, $message), JLog::ERROR);

			return false;
		}
	}

	public function change_stage() {
		$input    = JFactory::getApplication()->input;
		$formData = new JInput($input->get('jform', '', 'array'));

		$stage = $formData->getInt("edit_stage");

		$pk = $input->getInt("id") ? $input->getInt("id") : "new";

		$session = JFactory::getSession();
		$session->set("edit_stage." . $pk, $stage);

		if ($input->getString("id")) {
			$url = "index.php?option=com_surveyforce&view=survey&layout=edit&id=" . (int) $input->get("id") . "#locate";
		} else {
			$url = "index.php?option=com_surveyforce&view=survey&layout=edit#locate";
		}
		$this->setRedirect($url, false);
	}

	public function mark() {
		$input = JFactory::getApplication()->input;
		$mark  = $input->getString("mark");

		$li = [
			"survey-details" => 0, "survey-settings" => 1, "survey-verify" => 2, "survey-final" => 3
		];

		$session = JFactory::getSession();
		$session->set("mark", $li[$mark]);

		echo true;
		exit;
	}

	public function add_setting() {
		$input = JFactory::getApplication()->input;

		$formData = new JInput($input->get('jform', '', 'array'));

		$stage = $formData->getInt("stage");

		$session = JFactory::getSession();
		$session->set("stage", $stage);

		if ($input->getString("id")) {
			$url = "index.php?option=com_surveyforce&view=survey&layout=edit&id=" . (int) $input->get("id");
		} else {
			$url = "index.php?option=com_surveyforce&view=survey&layout=edit";
		}
		$this->setRedirect($url, false);
	}

	public function checkStore() {

		$app   = JFactory::getApplication();
		$stage = $app->input->getInt("stage");
		$id    = $app->input->getInt("id");

		$model = $this->getModel();
		echo $model->checkStore($id, $stage);

		exit;
	}

}
