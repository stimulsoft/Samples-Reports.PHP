<?php
require_once '../vendor/autoload.php';

use Stimulsoft\Report\StiReport;


// Creating a report object and set the necessary javascript options
$report = new StiReport();
$report->javascript->useRelativeUrls = false;
$report->javascript->appendHead('<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">');

// Processing the request and, if successful, immediately printing the result
$report->process();

// Loading a report by URL
// This method does not load the report object on the server side, it only generates the necessary JavaScript code
// The report will be loaded into a JavaScript object on the client side
$report->loadFile('../reports/SimpleList.mrt');

// Calling the report build
// This method does not render the report on the server side, it only generates the necessary JavaScript code
// The report will be rendered using a JavaScript engine on the client side
$report->render();

// Calling the report print
// This method does not print the report to the printer, it only calls the browser print dialog
$report->print();

// Displaying the visual part of the report as a prepared HTML page
$report->printHtml();
