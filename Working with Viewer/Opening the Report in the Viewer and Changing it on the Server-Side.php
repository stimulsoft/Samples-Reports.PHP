<?php
require_once '../vendor/autoload.php';

use Stimulsoft\Events\StiReportEventArgs;
use Stimulsoft\Report\StiReport;
use Stimulsoft\Viewer\StiViewer;


// Creating a viewer object and set the necessary javascript options
$viewer = new StiViewer();
$viewer->javascript->useRelativeUrls = false;
$viewer->javascript->appendHead('<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">');

// Defining viewer events before processing
// It is allowed to assign a PHP function, or the name of a JavaScript function, or a JavaScript function as a string
// Also it is possible to add several functions of different types using the append() method
$viewer->onOpenedReport = function (StiReportEventArgs $args) {

    // You can change any fields of the report object passed in the args
    $args->report->ReportAlias = 'Report Alias from Server-Side';

    // Or you can upload a new one
    $reportJsonString = file_get_contents('../reports/SimpleList.mrt');
    $args->setReportJson($reportJsonString);
};

// Processing the request and, if successful, immediately printing the result
$viewer->process();

// Creating a report object
$report = new StiReport();

// Loading a report by URL
// This method does not load the report object on the server side, it only generates the necessary JavaScript code
// The report will be loaded into a JavaScript object on the client side
$report->loadFile('../reports/SimpleList.mrt');

// Assigning a report object to the viewer
$viewer->report = $report;

// Displaying the visual part of the viewer as a prepared HTML page
$viewer->printHtml();
