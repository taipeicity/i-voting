<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * Import Controller
 */
class SurveyforceControllerImport extends JControllerForm {

    public function __construct($config = array()) {
		
        parent::__construct($config);
    }

	// 上傳檔案
	public function upload() {
		$model = $this->getModel();

		$app = JFactory::getApplication();
		$surv_id = $app->input->getInt('surv_id');
		$files = $app->input->files->get('import_file');
		$user = JFactory::getUser();
		$user_id = $user->id;
		$user_name = $user->name;

		$link = "index.php?option=com_surveyforce&view=import&surv_id=". $surv_id;

		// 檢查檔案副檔名
		$type_list = "csv";
		if(!preg_match("/^.*\.(". $type_list. ")$/i", $files['name'])) {
			$this->setRedirect($link, "請重新選擇檔案，僅允許上傳 csv 檔案。");
			return false;
		}

		if (($handle = fopen($files['tmp_name'], "r")) !== FALSE) {
			fgetcsv($handle, 1000, ",");	// 第一列不用匯入
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {		// "題目", "選項", "子選項", "票數"
				if ($data[0] == "" && $data[1] == "") {		// 若遇空白列，代表已無資料
					break;
				}

				$votes[] = (int) $data[3];
			}
			fclose($handle);

			$questions = $model->getQuestions($surv_id);
			if ($questions) {
				// 取出舊資料
				$option_vote = $model->getPaperVoteByOption($surv_id);
				$sub_option_vote = $model->getPaperVoteBySubOption($surv_id);
				
				// 先刪除舊資料
				$model->deletePaperVote($surv_id);

				$count = 0;
				$created = JFactory::getDate()->toSql();
				$code = date("YmdHis");
				
				// 寫入記錄總覽
				$model->savePaperVoteSummary($surv_id, $code, $created, $user_id, $user_name );

				foreach ($questions as $question) {
					// 檢查是否有子選項
					$sub_options = $model->getSubOptions($question->question_id);
					if ($sub_options) {
						foreach ($sub_options as $sub_option) {		// 寫入子選項 (要加總舊的票數)
							$model->savePaperVote($surv_id, $question->question_id, $question->option_id, $sub_option->sub_option_id, $votes[$count] + $sub_option_vote[$question->option_id. $sub_option->sub_option_id], $created );
							$model->savePaperVoteRecord($surv_id, $code, $question->question_id, $question->option_id, $sub_option->sub_option_id, $votes[$count], $created );
							$count++;
						}
					} else {
						$model->savePaperVote($surv_id, $question->question_id, $question->option_id, "", $votes[$count] + $option_vote[$question->option_id], $created );
						$model->savePaperVoteRecord($surv_id, $code, $question->question_id, $question->option_id, "", $votes[$count], $created );
						$count++;
					}
				}
			} else {
				$this->setRedirect($link, "匯入失敗，請檢查題目是否設定正確。");
				return false;
			}


		} else {
			$this->setRedirect($link, "無法開啟上傳的檔案，請重新執行。");
			return false;
		}


		$this->setRedirect($link, "匯入檔案成功，共匯入". $count. "筆資料。請至觀看結果功能中查看詳細資料。");
		return;

	}
	

	// 更新紙本投票人數
	public function updateVote() {
		$db =& JFactory::getDBO();
		$app = JFactory::getApplication();
		$surv_id = $app->input->getInt('surv_id');
		$paper_total_vote = $app->input->getInt('paper_total_vote');


		$link = "index.php?option=com_surveyforce&view=import&surv_id=". $surv_id;
		
		
		try {
			$db->transactionStart();

			// 更新
			$query = $db->getQuery(true);
			$fields = array(
				$db->quoteName('paper_total_vote') . ' = ' . $db->quote($paper_total_vote)
			);
			$conditions = array(
				$db->quoteName('id') . ' = ' . $db->quote($surv_id)
			);
			$query->update($db->quoteName('#__survey_force_survs'))->set($fields)->where($conditions);

			$db->setQuery($query);
			$db->execute();

			// 更新正式議題
			$query = $db->getQuery(true);
			$fields = array(
				$db->quoteName('paper_total_vote') . ' = ' . $db->quote($paper_total_vote)
			);
			$conditions = array(
				$db->quoteName('id') . ' = ' . $db->quote($surv_id)
			);
			$query->update($db->quoteName('#__survey_force_survs_release'))->set($fields)->where($conditions);

			$db->setQuery($query);
			$db->execute();


			$db->transactionCommit();
		} catch (Exception $e) {
			// catch any database errors.
			$db->transactionRollback();
			JErrorPage::render($e);
		}




		$this->setRedirect($link, "紙本投票人數更新成功。");
		return;


	}


	// 移除記錄
	public function deletePaper() {
		$model = $this->getModel();

		$app = JFactory::getApplication();
		$surv_id = $app->input->getInt('surv_id');
		$code = $app->input->getString('code');
		$created = JFactory::getDate()->toSql();

		$link = "index.php?option=com_surveyforce&view=import&surv_id=". $surv_id;
		
		
		// 取出紙本紀錄資料
		$vote_records = $model->getPaperVoteRecord($surv_id, $code);

		foreach ($vote_records as $vote_record) {
			if ($vote_record->vote_num > 0) {
				// 更新紙本紀錄的票數
				$model->updatePaperVote($surv_id, $vote_record->question_id, $vote_record->field_id, $vote_record->sub_field_id, $vote_record->vote_num, $created);
			}
		}

		// 刪除紙本紀錄
		$model->deletePaperVoteRecord($surv_id, $code);
		
		// 刪除紙本總覽
		$model->deletePaperVoteSummary($surv_id, $code);




		$this->setRedirect($link, "刪除紀錄成功。請至觀看結果功能中查看詳細資料。");
		return;

	}
	
	

}
