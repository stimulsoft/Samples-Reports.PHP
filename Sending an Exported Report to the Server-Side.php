<?php
require_once 'vendor/autoload.php';

use Stimulsoft\Events\StiExportEventArgs;
use Stimulsoft\Report\StiReport;
use Stimulsoft\StiResult;
use Stimulsoft\Viewer\StiViewer;


// Creating a viewer object
$viewer = new StiViewer();

// Defining viewer events before processing
// It is allowed to assign a PHP function, or the name of a JavaScript function, or a JavaScript function as a string
// Also it is possible to add several functions of different types using the append() method
$viewer->onEndExportReport = function (StiExportEventArgs $args) {

    // Getting the file name with the extension
    $reportName = $args->fileName;
    if (substr($reportName, -strlen($args->fileExtension) - 1) !== '.' . $args->fileExtension)
        $reportName .= '.' . $args->fileExtension;

    // Saving the exported file in the 'reports' folder
    $reportPath = "reports/$reportName";
    file_put_contents($reportPath, base64_decode($args->data));

    // If required, it is possible to show a message about success or some error
    return StiResult::getSuccess("The exported report has been successfully saved to '$reportPath' file.");
    //return StiResult::getError('An error occurred while exporting the report.');
};

// Processing the request and, if successful, immediately printing the result
$viewer->handler->process();

// Creating a report object
$report = new StiReport();

// Loading a report by URL
// This method does not load the report object on the server side, it only generates the necessary JavaScript code
// The report will be loaded into a JavaScript object on the client side
$report->loadFile('reports/SimpleList.mrt');

// Assigning a report object to the viewer
$viewer->report = $report;

// Displaying the visual part of the viewer as a prepared HTML page
$viewer->printHtml();
