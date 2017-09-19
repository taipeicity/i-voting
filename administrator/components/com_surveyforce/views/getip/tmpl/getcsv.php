<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die;

$path = str_replace("administrator/", "", JPATH_COMPONENT);
include_once $path . '/helpers/vote.php';

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$filename = 'IPsource';

$app = JFactory::getApplication();
$surv_id = $app->input->getInt("surv_id"); //取得議題id
$source_type = $app->input->getInt("source_type"); //取得欲匯出的ip
//取得ip範圍
$menu = JComponentHelper::getParams('com_surveyforce');
$Internal_IP = $menu->get('Internal_IP');
$rows_IP = explode(",", $Internal_IP);

$config = JFactory::getConfig();
$api_request_url = $config->get('agent_path') . "/server_poll_source.php";

$api_request_parameters = array(
    'survey_id' => $surv_id,
    'source_type' => $source_type,
    'rows_IP' => $rows_IP,
);
$api_result_json = SurveyforceVote::curlAPI($api_request_url, "GET", $api_request_parameters);
$api_result = json_decode($api_result_json);

ob_start();

if ($api_result) {
    foreach ($api_result as $api) {
        echo $api . "\r\n";
    }
}
echo "總計：,";
echo count($api_result) . "筆";

$output = ob_get_contents();
ob_end_clean();

header("Content-type: text/x-csv");

header("Content-Disposition: inline; filename=\"" . $filename . ".csv\"");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

echo mb_convert_encoding($output, "Big5", "UTF-8");
jexit();
?>
