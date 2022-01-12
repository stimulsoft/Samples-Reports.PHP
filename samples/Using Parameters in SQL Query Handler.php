<?php
require_once '../stimulsoft/helper.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token');
header('Cache-Control: no-cache');

$handler = new StiHandler();
$handler->registerErrorHandlers();

// Server-side event handler
// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_connecting_sql_data.htm
$handler->onBeginProcessData = function ($args) {
	// Current database type
	$database = $args->database;
	// Current connection name
	$connection = $args->connection;
	// Current data source name
	$dataSource = $args->dataSource;
	// Connection string of the current data source
	$connectionString = $args->connectionString;
	// SQL query string of the current data source
	$queryString = $args->queryString;
	
	// You can change the connection string
	/*
	if ($connection == 'MyConnectionName')
		$args->connectionString = 'Server=localhost;Database=test;uid=root;password=******;';
	*/
	
	// You can change the SQL query
	/*
	if ($dataSource == 'MyDataSource')
		$args->queryString = 'SELECT * FROM MyTable';
	*/
	
	// You can change the SQL query parameters with the required values
	// For example: SELECT * FROM @Parameter1 WHERE Id = @Parameter2 AND Date > @Parameter3
	/*
	if ($dataSource == 'MyDataSourceWithParams') {
		$args->parameters['Parameter1']->value = 'TableName';
		$args->parameters['Parameter2']->value = 10;
		$args->parameters['Parameter3']->value = '2019-01-20';
	}
	*/
	
	// SimpleListSQLParameters.mrt report template
	if ($dataSource == 'customers') {
		$args->parameters['Country']->value = "Germany";
	}
	
	// Returning the result of the event to the client-side
	// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_php_handler.htm
	return StiResult::success();
	//return StiResult::success('Warning or other useful information.');
	//return StiResult::error('A message about some connection error.');
};

$handler->process();