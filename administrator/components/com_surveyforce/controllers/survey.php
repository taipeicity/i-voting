<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die('Restricted access');

class SurveyforceControllerSurvey extends JControllerForm {

    protected $last_insert_id;

    public function __construct() {
        $this->_trackAssets = true;
        parent::__construct();
    }

    public function cancel() {
        $this->setRedirect('index.php?option=com_surveyforce&view=surveys');
    }

    protected function postSaveHook(JModelLegacy $model, $validData = array()) {
        $app = JFactory::getApplication();
        $config = JFactory::getConfig();

        $ivoting_path = $config->get('ivoting_path');
        $ivoting_save_path = $config->get('ivoting_save_path');
        $tmp_path = $config->get('tmp_path');

        $this->last_insert_id = $model->getState($model->getName() . '.id');

        $post = $app->input->getArray($_POST);

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


        // 上傳檔案
        if ($ivoting_path) {
            $upload_files = $app->input->files->get('jform');

            // 上傳banner檔
            $upload_file = $upload_files["image"];

            if (is_array($upload_file) && $upload_file["name"]) {
                if ($upload_file["error"] != 0) {
                    $result["msg"] = "上傳檔案失敗。";
                }

                // 檢查檔案大小
                if ($upload_file["size"] > 2097152) {
                    $result["msg"] = "上傳檔超過指定大小(2MB)。";
                }

                // 檢查副檔名
                $allow_files = array("image/jpeg", "image/pjpeg", "image/png", "image/gif");
                if (!in_array($upload_file["type"], $allow_files)) {
                    $result["msg"] = "只允許上傳圖片類型(jpg/png/gif)。";
                }


                // 上傳
                if ($result["msg"] == "") {
                    // 非JPG檔做轉換
                    if (exif_imagetype($upload_file['tmp_name']) != 2) {
                        JHtml::_('utility.transformImg', $upload_file['tmp_name'], $upload_file['tmp_name'], "jpeg");
                    }

                    // 壓縮圖片
                    JHtml::_('utility.compressImg', $tmp_path, $upload_file['tmp_name'], 80);

                    $old_image = $ivoting_path . "/survey/surveys/" . $this->last_insert_id . "_image" . ".jpg";
                    $desc_file = JPATH_SITE . "/" . $old_image;
                    JFile::upload($upload_file['tmp_name'], $desc_file);
                } else {
                    JError::raiseWarning(100, $result["msg"]);
                }
            } else {  // 未上傳Banner時，先檢查是否已有舊圖，若沒有給預設圖
                if ($app->input->getString('old_image')) {
                    $old_image = $app->input->getString('old_image');
                } else {
                    $default_img = "images/system/banner_default.jpg";
                    $dest_img = $ivoting_path . "/survey/surveys/" . $this->last_insert_id . "_image" . ".jpg";

                    if (copy(JPATH_SITE . "/" . $default_img, JPATH_SITE . "/" . $dest_img)) {
                        $old_image = $dest_img;
                    } else {
                        $old_image = "";
                    }
                }
            }

            // 更新image欄位
            $model->updateField("image", $old_image, $this->last_insert_id);


            // 上傳PDF檔
            $upload_pdfs["other_data"] = $upload_files["other_data"];
            $upload_pdfs["other_data2"] = $upload_files["other_data2"];
            $upload_pdfs["other_data3"] = $upload_files["other_data3"];
            $upload_pdfs["other_data"]["old_pdf"] = $app->input->getString('old_pdf');
            $upload_pdfs["other_data2"]["old_pdf"] = $app->input->getString('old_pdf2');
            $upload_pdfs["other_data3"]["old_pdf"] = $app->input->getString('old_pdf3');

            foreach ($upload_pdfs as $i => $upload_pdf) {
                if ($upload_pdf["name"]) {
                    switch ($upload_pdf["error"]) {

                        case 0: break;

                        // 檢查檔案大小
                        case 1:
                            $result_pdf["msg"] = "上傳檔超過指定大小(5MB)。";
                            break;

                        // 檢查檔案大小
                        case 2:
                            $result_pdf["msg"] = "上傳檔超過指定大小(5MB)。";
                            break;

                        case 3:
                        case 4:
                        case 5:
                        case 6:
                        case 7:

                        default:
                            $result_pdf["msg"] = "上傳檔案失敗。";
                            // 檢查副檔名
                            if (!in_array($upload_pdf["type"], $allow_files)) {
                                $result_pdf["msg"] .= "<br>只允許上傳PDF。";
                            }
                    }

                    // 上傳
                    if ($result_pdf["msg"] == "") {
                        $old_pdf = $ivoting_path . "/survey/pdf/" . $this->last_insert_id . "/" . $upload_pdf["name"];
                        $desc_pdf = JPATH_SITE . "/" . $old_pdf;
                        JFile::upload($upload_pdf["tmp_name"], $desc_pdf);
                    } else {
                        JError::raiseWarning(100, $result_pdf["msg"]);
                    }
                } else {
                    $old_pdf = $upload_pdf["old_pdf"];
                }
                // 更新other_data, other_data2, other_data3欄位
                $model->updateField($i, $old_pdf, $this->last_insert_id);
            }


            // 上傳掃描標的物圖片
            if ($validData["is_place"] == 1) {
                unset($upload_file);
                $upload_file = $upload_files["place_image"];


                if (is_array($upload_file) && $upload_file["name"]) {
                    if ($upload_file["error"] != 0) {
                        $result["msg"] = "上傳檔案失敗。";
                    }

                    // 檢查檔案大小
                    if ($upload_file["size"] > 2097152) {
                        $result["msg"] = "上傳檔超過指定大小(2MB)。";
                    }

                    // 檢查副檔名
                    $allow_files = array("image/jpeg", "image/pjpeg", "image/png", "image/gif");
                    if (!in_array($upload_file["type"], $allow_files)) {
                        $result["msg"] = "只允許上傳圖片類型(jpg/png/gif)。";
                    }


                    // 上傳
                    if ($result["msg"] == "") {
                        // 非JPG檔做轉換
                        if (exif_imagetype($upload_file['tmp_name']) != 2) {
                            JHtml::_('utility.transformImg', $upload_file['tmp_name'], $upload_file['tmp_name'], "jpeg");
                        }

                        // 壓縮圖片
                        JHtml::_('utility.compressImg', $tmp_path, $upload_file['tmp_name'], 80);

                        $old_place_image = $ivoting_path . "/survey/surveys/" . $this->last_insert_id . "_place_image" . ".jpg";
                        $desc_file = JPATH_SITE . "/" . $old_place_image;
                        JFile::upload($upload_file['tmp_name'], $desc_file);
                    } else {
                        JError::raiseWarning(100, $result["msg"]);
                    }
                } else {  // 未上傳圖片時，先檢查是否已有舊圖，若沒有給預設圖
                    if ($app->input->getString('old_place_image')) {
                        $old_place_image = $app->input->getString('old_place_image');
                    } else {
                        $default_img = "images/system/idnum_sample.jpg";
                        $dest_img = $ivoting_path . "/survey/surveys/" . $this->last_insert_id . "_place_image" . ".jpg";

                        if (copy(JPATH_SITE . "/" . $default_img, JPATH_SITE . "/" . $dest_img)) {
                            $old_place_image = $dest_img;
                        } else {
                            $old_place_image = "";
                        }
                    }
                }

                // 更新image欄位
                $model->updateField("place_image", $old_place_image, $this->last_insert_id);
            }
        } else {
            JError::raiseWarning(100, '存檔路徑尚未設置，請通知系統管理員。');
        }



        // 新增資料夾
        if (!is_dir($ivoting_save_path . "/ca/" . $this->last_insert_id)) {
            @mkdir($ivoting_save_path . "/ca/" . $this->last_insert_id, 0755);
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
    }

    public function save() {
        $task = JFactory::getApplication()->input->get('task');
        $save = parent::save();
    }

// 刪除
    public function delete() {
        $model = $this->getModel();
        $app = JFactory::getApplication();
        $jinput = $app->input;
        $jform = $app->input->get('jform', '', 'array');
        $survey_id = $jform['id'];

        $date = JFactory::getDate();
        $nowDate = $date->toSql();

        $user = JFactory::getUser();
        $user_id = $user->get('id');
        $unit_id = $user->get('unit_id');

        $survey = $model->getSurvey($survey_id);

        $created_user = JFactory::getUser($survey->created_by);
        $created_unit_id = $created_user->get('unit_id');

        $state = $this->get('State');
        $canDo = JHelperContent::getActions('com_surveyforce');


        $self_gps = JUserHelper::getUserGroups($user->get('id'));
        $core_review = JComponentHelper::getParams('com_surveyforce')->get('core_review');

// 作者 或 同單位審核者 或 最高權限 才可儲存和刪除
        if ($survey->created_by == $user_id || ($unit_id == $created_unit_id && in_array($core_review, $self_gps)) || $canDo->get('core.own')) {
// 是否為已投票
            if ($survey->complete && $survey->checked && ( strtotime($survey->vote_start) < strtotime($nowDate) ) && ( strtotime($survey->vote_end) > strtotime($nowDate) )) {
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
        $model = $this->getModel();
        $config = JFactory::getConfig();
        $app = JFactory::getApplication();
        $jinput = $app->input;
        $jform = $app->input->get('jform', '', 'array');

        $user = JFactory::getUser();
        $unit_id = $user->get('unit_id');


// 檢查是否有新增題目
        $questions = $model->getQuestions($jform['id']);

        if ($questions) {
//  檢查每個題目是否都有選項
            unset($mesg);
            $mesg = array();
            foreach ($questions as $question) {
                if ($model->getOptionsCount($question->id) <= 0) {
                    $mesg[] = "題目 - " . $question->sf_qtext . " 尚未新增選項";
                }
            }

            if (count($mesg) > 0) {
                JError::raiseWarning(100, implode("<br>", $mesg));
            } else {
// 更新欄位
                $model->updateField("is_complete", 1, $jform['id']);
                $model->updateField("is_checked", 0, $jform['id']);
                $model->updateField("published", 0, $jform['id']);

// 寄發Email郵件通知審核人員
                $users = $model->getUsersByUnit($unit_id);
                foreach ($users as $user) {
                    $groups = JAccess::getGroupsByUser($user->id, false);
                    if (in_array(4, $groups) && $user->email) {

                        $sitename = $config->get('sitename');
                        $from_email = $config->get('mailfrom');
                        $from_name = $config->get('fromname');

                        $subject = $sitename . "-議題審核通知";
                        $alert_msg = "<p>您好：<br><br>";
                        $alert_msg.= "新投票議題：「" . $jform['title'] . "」<br>";
                        $alert_msg.= "請盡速登入系統後台進行議題審核。<br><br>";
                        $alert_msg.= "" . $sitename . " 敬上<br><br>";
                        $alert_msg.= "◎備註：此信件由系統自動發出，請不要回覆。</p>";

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
        } else {
            JError::raiseWarning(100, '該議題未設定題目，請先新增題目。');
        }


        $this->cancel();
    }

    // 審核通過
    public function pass_success() {
        $model = $this->getModel();
        $config = JFactory::getConfig();
        $app = JFactory::getApplication();
        $jinput = $app->input;
        $jform = $app->input->get('jform', '', 'array');

// 檢查是否有新增題目
        $questions = $model->getQuestions($jform['id']);

        if ($questions) {
//  檢查每個題目是否都有選項
            unset($mesg);
            $mesg = array();
            foreach ($questions as $question) {
                if ($model->getOptionsCount($question->id) <= 0) {
                    $mesg[] = "題目 - " . $question->sf_qtext . " 尚未新增選項";
                }
            }

            if (count($mesg) > 0) {
                JError::raiseWarning(100, implode("<br>", $mesg));
            } else {

                $date = JFactory::getDate();
                $nowDate = $date->toSql();
                $user = JFactory::getUser();
                $user_id = $user->get('id');

// 更新欄位
                $model->updateField("is_complete", 1, $jform['id']);
                $model->updateField("is_checked", 1, $jform['id']);
                $model->updateField("published", 1, $jform['id']);
                $model->updateField("checked", $nowDate, $jform['id']);
                $model->updateField("checked_by", $user_id, $jform['id']);

// 寄發Email郵件通知承辦人員
                $user = JFactory::getUser($jform['created_by']);


                $sitename = $config->get('sitename');
                $from_email = $config->get('mailfrom');
                $from_name = $config->get('fromname');

                $subject = $sitename . "-議題審核通過通知";
                $alert_msg = "<p>您好：<br><br>";
                $alert_msg.= "投票議題：「" . $jform['title'] . "」<br>";
                $alert_msg.= "該議題已審核通過。<br><br>";
                $alert_msg.= "" . $sitename . " 敬上<br><br>";
                $alert_msg.= "◎備註：此信件由系統自動發出，請不要回覆。</p>";

                $send_email_status = JHtml::_('utility.sendMail', $from_email, $from_name, $user->email, $subject, $alert_msg, 1);


                if (is_object($send_email_status)) {
                    $send_email_status = 0;
                    JHtml::_('utility.recordLog', "debug_log.php", "議題ID:" . $jform['id'] . ",審核過通無法發送", JLog::ERROR);
                }

                $encode_email = JHtml::_('utility.endcode', $user->email);
                JHtml::_('utility.sendMailRecord', $send_email_status, $from_email, $from_name, $encode_email, $subject, $alert_msg, 1);


                JError::raiseNotice(100, '議題審核通過成功');
            }
        } else {
            JError::raiseWarning(100, '該議題未設定題目，請確認是否已新增題目。');
        }


        $this->cancel();
    }

// 審核不通過
    public function pass_fail() {
        $model = $this->getModel();
        $config = JFactory::getConfig();
        $app = JFactory::getApplication();
        $jinput = $app->input;
        $jform = $app->input->get('jform', '', 'array');

        $date = JFactory::getDate();
        $nowDate = $date->toSql();
        $user = JFactory::getUser();
        $user_id = $user->get('id');


// 更新欄位
        $model->updateField("is_complete", 0, $jform['id']);
        $model->updateField("is_checked", 0, $jform['id']);
        $model->updateField("published", 0, $jform['id']);
        $model->updateField("checked", $nowDate, $jform['id']);
        $model->updateField("checked_by", $user_id, $jform['id']);

// 寄發Email郵件通知承辦人員
        $user = JFactory::getUser($jform['created_by']);


        $sitename = $config->get('sitename');
        $from_email = $config->get('mailfrom');
        $from_name = $config->get('fromname');

        $subject = $sitename . "-議題審核不通過通知";
        $alert_msg = "<p>您好：<br><br>";
        $alert_msg.= "投票議題：「" . $jform['title'] . "」<br>";
        $alert_msg.= "該議題審核為不通過，原因如下：";
        $alert_msg.= $jform[fail_reason] . "<br><br>";
        $alert_msg.= "" . $sitename . " 敬上<br><br>";
        $alert_msg.= "◎備註：此信件由系統自動發出，請不要回覆。</p>";

        $send_email_status = JHtml::_('utility.sendMail', $from_email, $from_name, $user->get('email'), $subject, $alert_msg, 1);


        if (is_object($send_email_status)) {
            $send_email_status = 0;
            JHtml::_('utility.recordLog', "debug_log.php", "議題ID:" . $jform['id'] . ",審核過通無法發送", JLog::ERROR);
        }

        $encode_email = JHtml::_('utility.endcode', $user->get('email'));
        JHtml::_('utility.sendMailRecord', $send_email_status, $from_email, $from_name, $encode_email, $subject, $alert_msg, 1);


        JError::raiseNotice(100, '議題設為審核不通過，已通知議題承辦人員。');


        $this->cancel();
    }

// 重新審核
    public function recheck() {
        $model = $this->getModel();
        $config = JFactory::getConfig();
        $app = JFactory::getApplication();
        $jinput = $app->input;
        $jform = $app->input->get('jform', '', 'array');

        $user = JFactory::getUser();
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
                $sitename = $config->get('sitename');
                $from_email = $config->get('mailfrom');
                $from_name = $config->get('fromname');

                $subject = $sitename . "-議題重新審核通知";
                $alert_msg = "<p>您好：<br><br>";
                $alert_msg.= "投票議題：「" . $jform['title'] . "」<br>";
                $alert_msg.= "請盡速登入系統後台進行議題重新審核。<br><br>";
                $alert_msg.= "" . $sitename . " 敬上<br><br>";
                $alert_msg.= "◎備註：此信件由系統自動發出，請不要回覆。</p>";

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

}
