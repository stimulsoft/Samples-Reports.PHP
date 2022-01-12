<?php
require_once '../stimulsoft/helper.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token');
header('Cache-Control: no-cache');

$handler = new StiHandler();
$handler->registerErrorHandlers();

// Server-side event handler
// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_viewer_export.htm
$handler->onEndExportReport = function ($args) {
	// File name of the exported report
	$fileName = $args->fileName;
	// File extension of the exported report
	$fileExtension = $args->fileExtension;
	// Current export format
	$format = $args->format;
	// Exported binary data in BASE64 format
	$data = $args->data;
	// Exported binary data
	$data = base64_decode($args->data);
	
	// For example, you can save an exported file to the 'reports' folder on the server-side
	file_put_contents('../reports/'.$args->fileName.'.'.$fileExtension, $data);
	
	// Returning the result of the event to the client-side
	// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_php_handler.htm
	//return StiResult::success();
	return StiResult::success('The exported report was saved successfully: '.$args->fileName.'.'.$fileExtension);
	//return StiResult::error('An error occurred while saving the exported report: '.$fileName);
};

$handler->process();