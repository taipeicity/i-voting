<?php

/**
 * @package            Surveyforce
 * @version            1.1-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');

class SurveyforceTableSurvey extends JTable {

	function __construct(&$db) {
		parent::__construct('#__survey_force_survs', 'id', $db);

	}

	protected function _getAssetName() {
		$k = $this->_tbl_key;

		return 'com_surveyforce.survey.' . (int) $this->$k;

	}

	protected function _getAssetTitle() {
		return $this->sf_name;

	}

	protected function _getAssetParentId() {
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_surveyforce');

		return $asset->id;

	}

	public function store($updateNulls = false) {
		$app  = JFactory::getApplication();
		$post = $app->input->getArray($_POST);

		$date = JFactory::getDate();
		$user = JFactory::getUser();

		if ($this->id) {
			// Existing item
			$this->modified    = $date->toSql();
			$this->modified_by = $user->get('id');

			if ($this->is_checked) {
				$this->checked = $date->toSql();
			}

			$is_store                      = json_decode($this->is_store, true);
			$is_store[$post['edit_stage']] = true;

			// 儲存下一階段就恢復成草稿狀態
			if ($this->stage > $post['current_stage']) {
				$this->is_complete = 0;
				$this->is_checked  = 0;
				$this->published   = 0;
			}

		} else {
			if (!(int) $this->created) {
				$this->created = $date->toSql();
			}

			if (empty($this->created_by)) {
				$this->created_by = $user->get('id');
			}

			$is_store                      = [1 => false, 2 => false, 3 => false, 4 => false, 5 => false, 6 => false];
			$is_store[$post['edit_stage']] = true;
		}

		$this->is_store = json_encode($is_store);

		if (isset($this->launched_date)) {
			// 投票公布日期設定
			if ($this->launched_date != 2) {
				$this->announcement_date = "";
			}
		}

		if ($this->vote_start and $this->vote_end) {
			// 自行帶入欄位
			$this->during_vote = JHtml::_('date', $this->vote_start, "Y年n月j日G點i分") . " 至 " . JHtml::_('date', $this->vote_end, "Y年n月j日G點i分");
		}

		// 預計投票人驗證方式規劃
		if (is_array($this->discuss_verify)) {
			$this->discuss_verify = json_encode($this->discuss_verify);
		}


		if ($this->discuss_vote_start and $this->discuss_vote_end){
			// 自行帶入欄位
			$this->discuss_vote_time = JHtml::_('date', $this->discuss_vote_start, "Y年n月j日") . " 至 " . JHtml::_('date', $this->discuss_vote_end, "Y年n月j日");
		}

		if ($post["session_stage"] > 4) {

			// 簡訊帳號及密碼 加密
			$this->sms_user   = JHtml::_('utility.endcode', $this->sms_user);
			$this->sms_passwd = JHtml::_('utility.endcode', $this->sms_passwd);

			// 驗證方式
			if ($post["is_old_verify"] == 0) { // 更換新的驗證方式
				unset($new_verify_array);
				switch ($post["verify_method"]) {
					case 0:
						$this->verify_required = 1;
						$new_verify_array      = array ("none");
						$verify_params         = "";
						break;
					case 1:  // 依強度選擇驗證方式
						$this->verify_required = 0;

						$new_verify_array = array ($post["verify_mix"]);

						break;
					case 2:  // 自訂驗證
						$this->verify_required = $post["verify_required"];

						$new_verify_array = $post["verify_custom"];
						break;
				}

				if ($new_verify_array) {
					$this->verify_type = json_encode($new_verify_array);

					unset($verify_params);
					foreach ($new_verify_array as $verify) {
						$className = 'plgVerify' . ucfirst($verify);

						// 儲存params
						if (method_exists($className, 'onAdminSaveParams')) {
							$verify_params[$verify] = $className::onAdminSaveParams($post);
						}
					}

					$this->verify_params = json_encode($verify_params);
				}
			}

			// 取得驗證方式
			$this->voters_authentication = SurveyforceHelper::getVerifyName($this->verify_type);
		}


		return parent::store($updateNulls);

	}

	public function check() {
		$app  = JFactory::getApplication();
		$post = $app->input->getArray($_POST);

		if ($post["session_stage"] > 4) {

			//投票模式
			if (count($this->vote_pattern) == 2) {
				$this->vote_pattern = 3;
			} else {
				$this->vote_pattern = $this->vote_pattern[0];
			}

			// 投票數設定
			if ($post["vote_num_type"] == 1) {
				$vote_num_params["vote_num_type"] = 1;
				$vote_num_params["vote_day"]      = $post["vote_num_type_vote_day"];
				$vote_num_params["vote_num"]      = $post["vote_num_type_vote_num"];
			} else {
				$vote_num_params["vote_num_type"] = 0;
				$vote_num_params["vote_day"]      = "";
				$vote_num_params["vote_num"]      = "";
			}

			// 防止灌票機制
			if ($post["vote_num_protect"] == 1) {
				$vote_num_params["vote_num_protect"]      = 1;
				$vote_num_params["vote_num_protect_time"] = $post["vote_num_protect_time"];
				$vote_num_params["vote_num_protect_vote"] = "";
			} else if ($post["vote_num_protect"] == 2) {
				$vote_num_params["vote_num_protect"]      = 2;
				$vote_num_params["vote_num_protect_time"] = "";
				$vote_num_params["vote_num_protect_vote"] = $post["vote_num_protect_vote"];
			} else {
				$vote_num_params["vote_num_protect"]      = 0;
				$vote_num_params["vote_num_protect_time"] = "";
				$vote_num_params["vote_num_protect_vote"] = "";
			}

			$this->vote_num_params = json_encode($vote_num_params);

			// 投票結果數設定
			if ($this->result_num_type != 1) {
				$this->result_num = 1;
			}

			// 郵件訊息設定
			if ($this->is_notice_email != 1) {
				$this->remind_text = "";
				$this->drumup_text = "";
				$this->end_text    = "";
			}

			// 手機訊息設定
			if ($this->is_notice_phone != 1) {
				$this->phone_remind_text = "";
				$this->phone_drumup_text = "";
				$this->phone_end_text    = "";
			}

			// 驗證方式
			if ($post["is_old_verify"] == 0) { // 更換新的驗證方式
				unset($new_verify_array);
				if ($post["verify_method"] == 1) { // 依強度選擇驗證方式
					$new_verify_array = array ($post["verify_mix"]);
				} else if ($post["verify_method"] == 2) {
					if ($post["verify_custom"] == "") {
						$this->setError("請至少選擇一種驗證方式。");

						return false;
					}

					if ($post["verify_required"] == 1) {
						if (count($post["verify_custom"]) < 2) {
							$this->setError("驗證組合方式為同時，請至少選擇兩種驗證方式。");

							return false;
						}
					}

					$new_verify_array = $post["verify_custom"];
				}


				if ($new_verify_array) {
					foreach ($new_verify_array as $verify) {
						JPluginHelper::importPlugin('verify', $verify);
						$className = 'plgVerify' . ucfirst($verify);

						// 檢查欄位是否有填寫
						if (method_exists($className, 'onAdminCheckVerify')) {
							$result = json_decode($className::onAdminCheckVerify($post));
							if ($result->status == 0) {
								$this->setError($result->msg);

								return false;
							}
						}
					}
				}
			} else { // 未更換驗證方式
				$new_verify_array = json_decode($post["is_old_verify_type"]);
			}

			// 現地投票設定
			if ($this->is_place == 1) {
				// 檢查是否有選擇身分證字號驗證
				if (!in_array("idnum", $new_verify_array)) {
					$this->setError("啟用現地投票，請選擇身分證字號驗證。");

					return false;
				}
			} else {
				$this->place_image = "";
			}
		}

		return true;

	}

}
