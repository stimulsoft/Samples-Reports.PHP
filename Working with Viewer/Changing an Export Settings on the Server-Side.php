<?php
require_once '../vendor/autoload.php';

use Stimulsoft\Events\StiExportEventArgs;
use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\Export\StiPdfExportSettings;
use Stimulsoft\Report\StiReport;
use Stimulsoft\Viewer\StiViewer;


// Creating a viewer object and set the necessary javascript options
$viewer = new StiViewer();
$viewer->javascript->relativePath = '../';
$viewer->javascript->appendHead('<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">');

// Defining viewer events before processing
// It is allowed to assign a PHP function, or the name of a JavaScript function, or a JavaScript function as a string
// Also it is possible to add several functions of different types using the append() method
$viewer->onBeginExportReport = function (StiExportEventArgs $args) {

    // You can change the file name of the exported report
    $args->fileName = "MyExportedFileName.$args->fileExtension";

    // You can change export settings, the set of settings depends on the export type
    if ($args->format == StiExportFormat::Pdf) {
        /** @var StiPdfExportSettings $settings */
        $settings = $args->settings;
        $settings->creatorString = 'My Company Name';
        $settings->keywordsString = 'SimpleList PHP Report Export';
        $settings->embeddedFonts = false;
    }
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