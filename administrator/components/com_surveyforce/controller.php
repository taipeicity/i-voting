<?php

/**
 * @package            Surveyforce
 * @version            1.1-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Surveyforce Component Controller
 */
class SurveyforceController extends JControllerLegacy {

    /**
     * display task
     *
     * @return void
     */
    public function __construct($config = array()){

        parent::__construct($config);

    }

    public function display($cachable = false, $urlparams = array()){
        $app = JFactory::getApplication();
        $view = JFactory::getApplication()->input->getCmd('view', 'surveys');

        switch($view){
            case "surveys":
            case "survey":
            case "questions":
            case "question":
            case "review":
            case "import":
            case "result":
            case "resultnote":
            case "export":
            case "getip":
            case "autocheck":
            case "print":
            case "lottery":
            case "addend":
            case "analyzes":
            case "analyze":
	        case "voted":
                break;
            default:
                $app->redirect("index.php?option=com_surveyforce&view=surveys"); // 直接進入議題管理
        }


        JFactory::getApplication()->input->set('view', $view);
        parent::display($cachable);

    }

    public function get_options(){

        if(!class_exists('SfAppPlugins')){
            include_once JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'app_plugin.php';
            $appsLib = SfAppPlugins::getInstance();
            $appsLib->loadApplications();
        }

        $type = SurveyforceHelper::getQuestionType(JFactory::getApplication()->input->getCmd('sf_qtype'));
        $data['quest_type'] = $type->sf_plg_name;
        $data['quest_id'] = JFactory::getApplication()->input->get('quest_id');
        $data['sf_qtype'] = JFactory::getApplication()->input->getCmd('sf_qtype');

        $appsLib->triggerEvent('onGetAdminQuestionOptions', $data);

    }

    // 投票測試
    public function testvote(){
        $db = JFactory::getDBO();
        $config = JFactory::getConfig();
        $testsite_link = $config->get('testsite_link', false);

        $app = JFactory::getApplication();
        $survey_id = $app->input->getInt('test_survey_id', 0);
        $api_request_url = $config->get('testsite_link', '') . "api/server_survey.php";

        // 先刪除資料
        $api_request_parameters = array('survey_id' => $survey_id);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $api_request_url .= '?' . http_build_query($api_request_parameters);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_URL, $api_request_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $api_response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $message = curl_error($ch);
        curl_close($ch);


        if($code == 200){
            $result_data = json_decode($api_response);

            if($result_data->status == 0){
                echo "無法匯入資料，請聯絡系統管理員。";
                JHtml::_('utility.recordLog', "api_log.php", "投票測試無法刪除資料", JLog::ERROR);
                jexit();
            }
        }else{ // 無法連線API
            echo "無法連線API，請聯絡系統管理員。";
            JHtml::_('utility.recordLog', "api_log.php", sprintf("Url:%s, Code:%d, Msg:%s", $api_request_url, $code, $message), JLog::ERROR);
            jexit();
        }

        // 再新增資料
        // 依序取得議題、題目、選項的資料
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__survey_force_survs_release');
        $query->where('id = ' . $db->quote($survey_id));
        $db->setQuery($query);
        $survey_row = $db->loadAssoc();

        // 取得題目
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__survey_force_quests');
        $query->where('sf_survey = ' . $db->quote($survey_id));
        $db->setQuery($query);
        $question_rows = $db->loadAssocList();


        unset($options_rows);
        $options_rows = array();

        unset($sub_options_rows);
        $sub_options_rows = array();

        unset($cats_rows);
        $cats_rows = array();
        if($question_rows){
            $question_ids = array();
            foreach($question_rows as $question_row){
                $question_ids[] = $question_row["id"];
            }

            // 取得選項
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__survey_force_fields');
            $query->where('quest_id IN (' . implode(",", $question_ids) . ')');
            $db->setQuery($query);
            $options_rows = $db->loadAssocList();

            // 取得子選項
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__survey_force_sub_fields');
            $query->where('quest_id IN (' . implode(",", $question_ids) . ')');
            $db->setQuery($query);
            $sub_options_rows = $db->loadAssocList();


            // 取得分類
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__survey_force_quests_cat');
            $query->where('question_id IN (' . implode(",", $question_ids) . ')');
            $db->setQuery($query);
            $cats_rows = $db->loadAssocList();
        }

        // assign_summary
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__assign_summary');
        $query->where('survey_id = ' . $db->quote($survey_id));
        $db->setQuery($query);
        $assign_summary_rows = $db->loadAssocList();


        // 寫至測試站台中，再將網頁導向測試站台
        $api_request_parameters = array('survey_id' => $survey_id, 'survey_row' => json_encode($survey_row), 'question_rows' => json_encode($question_rows), 'options_rows' => json_encode($options_rows), 'sub_options_rows' => json_encode($sub_options_rows), 'assign_summary_rows' => json_encode($assign_summary_rows), 'cats_rows' => json_encode($cats_rows));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_request_parameters));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_URL, $api_request_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $api_response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $message = curl_error($ch);
        curl_close($ch);


        if($code == 200){
            $result_data = json_decode($api_response);

            if($result_data->status == 0){
                echo "無法匯入議題資料，請聯絡系統管理員。";
                JHtml::_('utility.recordLog', "api_log.php", "投票測試無法新增資料", JLog::ERROR);
                jexit();
            }else{
                header("Location:" . $testsite_link . "index.php?option=com_surveyforce&view=intro&Itemid=120&sid=" . $survey_id);
                jexit();
            }
        }else{ // 無法連線API
            echo "無法連線API，請聯絡系統管理員。";
            JHtml::_('utility.recordLog', "api_log.php", sprintf("Url:%s, Code:%d, Msg:%s", $api_request_url, $code, $message), JLog::ERROR);
            jexit();
        }

        jexit();

    }

}
