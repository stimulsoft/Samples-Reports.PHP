<?php
require_once '../stimulsoft/helper.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token');
header('Cache-Control: no-cache');

$handler = new StiHandler();
$handler->registerErrorHandlers();

// Server-side event handler
// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_using_variables.htm
$handler->onPrepareVariables = function ($args) {
	
	// You can change the values of variables of various types
	/*
	// Simple types
	$args->variables['VariableString']->value = 'Value from Server-Side';
	$args->variables['VariableInt']->value = 123;
	$args->variables['VariableDecimal']->value = 123.456;
	$args->variables['VariableDateTime']->value = '2021-03-20 22:00:00';
	
	// Range
	$args->variables['VariableStringRange']->value->from = 'Aaa';
	$args->variables['VariableStringRange']->value->to = 'Zzz';
	
	// List
	$args->variables['VariableStringList']->value[0] = 'Test';
	$args->variables['VariableStringList']->value = ['1', '2', '2'];
	*/
	
	// Variables.mrt report template
	$args->variables['Name']->value = 'Maria';
	$args->variables['Surname']->value = 'Anders';
	$args->variables['Email']->value = 'm.anders@stimulsoft.com';
	$args->variables['Address']->value = 'Obere Str. 57, Berlin';
	$args->variables['Sex']->value = false;
	$args->variables['BirthDay']->value = '1982-03-20 00:00:00';
	
	// Returning the result of the event to the client-side
	// Documentation: https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_php_handler.htm
	return StiResult::success();
};


$handler->process();