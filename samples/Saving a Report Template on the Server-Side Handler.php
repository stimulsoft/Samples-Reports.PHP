<?php
require_once '../stimulsoft/helper.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token');
header('Cache-Control: no-cache');

$handler = new StiHandler();
$handler->registerErrorHandlers();

// Server-side event handler
// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_designer_saving_report.htm
$handler->onSaveReport = function ($args) {
	// Report object on the server-side
	$report = $args->report;
	// Report in the JSON format
	$reportJson = $args->reportJson;
	// Report file name
	$fileName = $args->fileName;
	
	// For example, you can save a template to the 'reports' folder on the server-side
	file_put_contents('../reports/'.$fileName.'.mrt', $reportJson);
	
	// Returning the result of the event to the client-side
	// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_php_handler.htm
	//return StiResult::success();
	return StiResult::success('The report was saved successfully: '.$fileName);
	//return StiResult::error('An error occurred while saving the report: '.$fileName);
};

$handler->process();