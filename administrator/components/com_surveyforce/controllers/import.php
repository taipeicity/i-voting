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

		$db =& JFactory::getDBO();
		$app = JFactory::getApplication();
		$surv_id = $app->input->getInt('surv_id');
		$files = $app->input->files->get('import_file');


		$link = "index.php?option=com_surveyforce&view=import&surv_id=". $surv_id;

		// 檢查檔案副檔名
		$type_list = "csv";
		if(!preg_match("/^.*\.(". $type_list. ")$/i", $files['name'])) {
			$this->setRedirect($link, "請重新選擇檔案，僅允許上傳 csv 檔案。");
			return false;
		}

		if (($handle = fopen($files['tmp_name'], "r")) !== FALSE) {

			unset($votes);
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
				// 先刪除舊資料
				$model->deletePaperVote($surv_id);

				$count = 0;
				$created = JFactory::getDate()->toSql();

				foreach ($questions as $question) {
					// 檢查是否有子選項
					$sub_options = $model->getSubOptions($question->question_id);
					if ($sub_options) {
						foreach ($sub_options as $sub_option) {		// 寫入子選項
							$model->savePaperVote($surv_id, $question->question_id, $question->option_id, $sub_option->sub_option_id, $votes[$count], $created );
							$count++;
						}
					} else {
						$model->savePaperVote($surv_id, $question->question_id, $question->option_id, "", $votes[$count], $created );
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



}
