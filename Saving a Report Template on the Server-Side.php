<?php
require_once 'vendor/autoload.php';

use Stimulsoft\Designer\StiDesigner;
use Stimulsoft\Events\StiReportEventArgs;
use Stimulsoft\Report\StiReport;
use Stimulsoft\StiResult;


// Creating a designer object
$designer = new StiDesigner();

// Defining designer events before processing
// It is allowed to assign a PHP function, or the name of a JavaScript function, or a JavaScript function as a string
// Also it is possible to add several functions of different types using the append() method
$designer->onSaveReport = function (StiReportEventArgs $args)
{
    // Getting the correct file name of the report template
    $reportFileName = strlen($args->fileName) > 0 ? $args->fileName : 'Report.mrt';
    if (strlen($reportFileName) < 5 || substr($reportFileName, -4) !== '.mrt')
        $reportFileName .= '.mrt';

    // Saving the report file in the 'reports' folder on the server-side
    $reportPath = "reports/$reportFileName";
    $result = file_put_contents($reportPath, $args->getReportJson());

    // If required, it is possible to show a message about success or some error
    if ($result === false)
        return StiResult::getError('An error occurred while saving the report file on the server side.');
    return StiResult::getSuccess("The report has been successfully saved to '$reportPath' file.");
    //return StiResult::getSuccess();
};

// Processing the request and, if successful, immediately printing the result
$designer->process();

// Creating a report object
$report = new StiReport();

// Loading a report by URL
// This method does not load the report object on the server side, it only generates the necessary JavaScript code
// The report will be loaded into a JavaScript object on the client side
$report->loadFile('reports/SimpleList.mrt');

// Assigning a report object to the designer
$designer->report = $report;

// Displaying the visual part of the designer as a prepared HTML page
$designer->printHtml();
