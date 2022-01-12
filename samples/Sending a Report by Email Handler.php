<?php
require_once '../stimulsoft/helper.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token');
header('Cache-Control: no-cache');

$handler = new StiHandler();
$handler->registerErrorHandlers();


// Server-side event handler
// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_viewer_send_email.htm
$handler->onEmailReport = function ($args) {
	// Email address of the sender
	$args->settings->from = "mail.sender@stimulsoft.com";
	// Address of the SMTP server
	$args->settings->host = "smtp.stimulsoft.com";
	// Port of the SMTP server
	$args->settings->port = 456;
	// Login (Username or Email)
	$args->settings->login = "********";
	// Password
	$args->settings->password = "********";
	
	// Returning the result of the event to the client-side
	// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_php_handler.htm
	return StiResult::success();
	//return StiResult::success('Warning or other useful information.');
	//return StiResult::error('A message about some error.');
};


$handler->process();