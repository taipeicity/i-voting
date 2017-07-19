<?php
define('_JEXEC', 1);

define('JPATH_BASE', '../../../../');

define('DS', DIRECTORY_SEPARATOR);

require_once ( JPATH_BASE . 'includes' . DS . 'defines.php' );
require_once ( JPATH_BASE . 'includes' . DS . 'framework.php' );

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

//JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;
$mainframe = & JFactory::getApplication('site');
$mainframe->initialise();

jimport('joomla.factory');
jimport('joomla.filesystem.file');

unset($result);
$result = array("status" => 0, "msg" => "");

$app = JFactory::getApplication();

$upload_file = $app->input->files->get('text_upload_file');

if ( is_array($upload_file)  && $upload_file["name"] ) {
	if ($upload_file["error"] != 0 ) {
		$result["msg"] = "上傳檔案失敗。";
	}

	// 檢查檔案大小
	if ($upload_file["size"] > 20971520) {
		$result["msg"] = "上傳檔超過指定大小(20MB)。";
	}

	// 檢查副檔名
	$allow_files = array("video/mp4");
	if ( !in_array($upload_file["type"], $allow_files) ) {
		$result["msg"] = "只允許上傳的影片類型為：mp4";
	}
	
	
	// 上傳
	if ($result["msg"] == "") {
		$file_ext = strtolower(substr($upload_file['name'], strrpos( $upload_file['name'] , '.') + 1));
		$filepath =  "/tmp/". time(). ".". $file_ext;
		$tmp_dest = JPATH_SITE. $filepath;

		if (JFile::upload( $upload_file['tmp_name'], $tmp_dest )) {
			$result["filepath"] = $filepath;
			$result["status"] = 1;
		} else {
			$result["msg"] = "移動檔案失敗。";
		}
	}

} else {
	$result["msg"] = "上傳檔案失敗。";
}



echo json_encode($result);