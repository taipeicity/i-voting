<?php
/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

define('_JEXEC', 1);


define('JPATH_BASE', '../../../../../../');

define('DS', DIRECTORY_SEPARATOR);

require_once(JPATH_BASE . 'includes' . DS . 'defines.php');
require_once(JPATH_BASE . 'includes' . DS . 'framework.php');

header('Content-type: application/json');

// Expires in the past
header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
// Always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
// HTTP/1.0
header("Pragma: no-cache");


$mainframe = &JFactory::getApplication('site');
$mainframe->initialise();

JHtml::_('bootstrap.tooltip');

jimport('joomla.factory');

$config          = JFactory::getConfig();
$app             = JFactory::getApplication();
$post            = $app->input->getArray(array ());
$verify_type     = $app->input->getString('verify_type');

$_agent_path     = $config->get('agent_path');
$api_request_url = $_agent_path . "/server_poll.php";

unset($api_request_parameters);
foreach ($post as $key => $item) {

	if ($verify_type == 'email') {

		if (preg_match('/^([^@\s]+)@gmail.com$/', $item, $match)) {
			$new_account = str_replace(".", "", $match[1]);
			if (strpos($new_account, "+")) {
				$new_account = substr($new_account, 0, strpos($new_account, "+"));
			}
			$api_request_parameters[$key] = strtolower($new_account . "@gmail.com");
		} else {
			$api_request_parameters[$key] = strtolower($item);
		}

	} else {

		$api_request_parameters[$key] = trim($item);

	}

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
		$result["content"] = $response->msg;
		$result["status"]  = 1;
	} else {
		$result["msg"]    = $response->msg;
		$result["status"] = 0;
	}
} else {
	// 記錄log
	JHtml::_('utility.recordLog', "api_log.php", sprintf("Url:%s, Code:%d, Msg:%s", $api_request_url, $code, $message), JLog::ERROR);
	$result["msg"] = "寫入資料至Agent伺服器失敗。";
}

echo json_encode($result);

exit();
