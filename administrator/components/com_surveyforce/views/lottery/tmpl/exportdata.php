<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$filename = 'lotterylist';


header("Content-type: text/x-csv");

header("Content-Disposition: inline; filename=\"" . $filename . ".csv\"");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

$app = JFactory::getApplication();
$config = JFactory::getConfig();

$agent_path = $config->get( 'agent_path' );
$api_request_url = $agent_path. "/server_lottery.php";
unset($api_request_parameters);
$api_request_parameters = array(
	'survey_id' => $this->surv_id
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$api_request_url .= '?' . http_build_query($api_request_parameters);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
curl_setopt($ch, CURLOPT_URL, $api_request_url);
$api_response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$message = curl_error($ch);

curl_close($ch);

if ($code == 200 ) {
	$result_data = json_decode($api_response);

	$out = fopen('php://output', 'w');
	fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF));
	if ($result_data->status == 1) {
		fputcsv($out, array("項次", "姓名", "電話", "填寫時間") );
		foreach ($result_data->data as $key => $row) {		// 寫入子選項
			fputcsv($out, array(($key+1), JHtml::_('utility.decode', $row->name), '"'. JHtml::_('utility.decode', $row->tel). '"', JHtml::_('date', $row->created, "Y-m-d H:i:s" )));
		}
	} else {
		fputcsv($out, array("**該議題並無任何抽獎資料。") );
	}

	fclose($out);

} else {
	// 記錄log
	JHtml::_('utility.recordLog', "api_log.php", sprintf("Url:%s, Code:%d, Msg:%s", $_url, $code, $message), JLog::ERROR);
}

jexit();
?>
