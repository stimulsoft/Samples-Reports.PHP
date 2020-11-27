<?php
require_once 'stimulsoft/helper.php';

// You can configure the security level as you required.
// By default is to allow any requests from any domains.

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token");
header('Cache-Control: no-cache');


$handler = new StiHandler();
$handler->registerErrorHandlers();


$handler->onBeginProcessData = function ($event) {
	
	// Current database type: 'XML', 'JSON', 'MySQL', 'MS SQL', 'PostgreSQL', 'Firebird', 'Oracle'
	$database = $event->database;
	// Current connection name
	$connection = $event->connection;
	// Current data source name
	$dataSource = $event->dataSource;
	// Connection string for the current data source
	$connectionString = $event->connectionString;
	// SQL query string for the current data source
	$queryString = $event->queryString;
	
	
	// You can change the connection string
	/*
	if ($connection == "MyConnectionName")
		$event->connectionString = "Server=localhost;Database=test;Port=3306;";
	*/
	
	
	// You can change the SQL query
	/*
	if ($dataSource == "MyDataSource")
		$event->queryString = "SELECT * FROM MyTable";
	*/
	
	
	// You can replace the SQL query parameters with the required values
	// For example: SELECT * FROM {Variable1} WHERE Id={Variable2}
	// If the report contains a variable with this name, its value will be used instead of the specified value
	/*
	$event->parameters["Variable1"] = "TableName";
	$event->parameters["Variable2"] = 10;
	*/
	
	
	// You can send a successful result:
	return StiResult::success();
	// You can send an informational message:
	//return StiResult::success("Warning or other useful information.");
	// You can send an error message:
	//return StiResult::error("A message about some connection error.");
};

$handler->onPrintReport = function ($event) {
	$fileName = $event->fileName; // Report file name
	
	return StiResult::success();
};

$handler->onBeginExportReport = function ($event) {
	$format = $event->format; // Export format
	$settings = $event->settings; // Export settions
	$fileName = $event->fileName; // Report file name
	
	return StiResult::success();
};

$handler->onEndExportReport = function ($event) {
	$format = $event->format; // Export format
	$data = $event->data; // Base64 export data
	$fileName = $event->fileName; // Report file name
	
	// By default, the exported file is saved to the 'reports' folder.
	// You can change this behavior if required.
	file_put_contents('reports/'.$fileName.'.'.strtolower($format), base64_decode($data));
	
	//return StiResult::success();
	return StiResult::success("Successful export of the report.");
	//return StiResult::error("An error occurred while exporting the report.");
};

$handler->onEmailReport = function ($event) {
	
	// These parameters will be used when sending the report by email. You must set the correct values.
	$event->settings->from = "******@gmail.com";
	$event->settings->host = "smtp.gmail.com";
	$event->settings->login = "******";
	$event->settings->password = "******";
	
	// These parameters are optional.
	//$event->settings->name = "John Smith";
	//$event->settings->port = 465;
	//$event->settings->cc[] = "copy1@gmail.com";
	//$event->settings->bcc[] = "copy2@gmail.com";
	//$event->settings->bcc[] = "copy3@gmail.com John Smith";
	
	return StiResult::success("Email sent successfully.");
};

$handler->onDesignReport = function ($event) {
	return StiResult::success();
};

$handler->onCreateReport = function ($event) {
	$fileName = $event->fileName;
	return StiResult::success();
};

$handler->onSaveReport = function ($event) {
	$report = $event->report; // Report object
	$reportJson = $event->reportJson; // Report in JSON format
	$fileName = $event->fileName; // Report file name
	
	file_put_contents('reports/'.$fileName.".mrt", $reportJson);
	
	//return StiResult::success();
	return StiResult::success("Save Report OK: ".$fileName);
	//return StiResult::error("Save Report ERROR. Message from server side.");
};

$handler->onSaveAsReport = function ($event) {
	return StiResult::success();
};


// Process request
$handler->process();
