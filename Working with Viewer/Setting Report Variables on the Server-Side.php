<?php
require_once '../vendor/autoload.php';

use Stimulsoft\Events\StiVariablesEventArgs;
use Stimulsoft\Report\StiReport;
use Stimulsoft\Viewer\StiViewer;


// Creating a viewer object and set the necessary javascript options
$viewer = new StiViewer();
$viewer->javascript->useRelativeUrls = false;
$viewer->javascript->appendHead('<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">');

// Defining viewer events before processing
// It is allowed to assign a PHP function, or the name of a JavaScript function, or a JavaScript function as a string
// Also it is possible to add several functions of different types using the append() method
$viewer->onPrepareVariables = function (StiVariablesEventArgs $args) {

    // You can set the values of the report variables, the value types must match the original types
    // If the variable contained an expression, the already calculated value will be passed
    // The new values will be passed to the report generator
    /*
    $args->variables['VariableString']->value = 'Value from Server-Side';
    $args->variables['VariableDateTime']->value = '2020-01-31 22:00:00';

    $args->variables['VariableStringRange']->value->from = 'Aaa';
    $args->variables['VariableStringRange']->value->to = 'Zzz';

    $args->variables['VariableStringList']->value[0] = 'Test';
    $args->variables['VariableStringList']->value = ['1', '2', '2'];

    $args->variables['NewVariable'] = ['value' => 'New Value'];
    */

    // Changing variables with the required values
    if (count($args->variables) > 0) {
        $args->variables['Name']->value = 'Maria';
        $args->variables['Surname']->value = 'Anders';
        $args->variables['Email']->value = 'm.anders@stimulsoft.com';
        $args->variables['Address']->value = 'Obere Str. 57, Berlin';
        $args->variables['Sex']->value = false;
        $args->variables['BirthDay']->value = '1982-03-20 00:00:00';
    }
};

// Processing the request and, if successful, immediately printing the result
$viewer->process();

// Creating a report object
$report = new StiReport();

// Loading a report by URL
// This method does not load the report object on the server side, it only generates the necessary JavaScript code
// The report will be loaded into a JavaScript object on the client side
$report->loadFile('../reports/Variables.mrt');

// Assigning a report object to the viewer
$viewer->report = $report;

// Displaying the visual part of the viewer as a prepared HTML page
$viewer->printHtml();
