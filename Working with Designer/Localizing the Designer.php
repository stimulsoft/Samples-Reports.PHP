<?php
require_once '../vendor/autoload.php';

use Stimulsoft\Designer\StiDesigner;
use Stimulsoft\Report\StiReport;


// Creating a designer object and set the necessary javascript options
$designer = new StiDesigner();
$designer->javascript->relativePath = '../';
$designer->javascript->appendHead('<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">');

// Defining the required interface localization
// The list of available localizations can be obtained from the GitHub repository:
// https://github.com/stimulsoft/Stimulsoft.Reports.Localization
$designer->options->localization = 'de.xml';

// Additionally, it is possible to add optional localizations
// They will be displayed in the localization menu in the designer panel
$designer->options->addLocalization('fr.xml');
$designer->options->addLocalization('es.xml');
$designer->options->addLocalization('pt.xml');

// Processing the request and, if successful, immediately printing the result
$designer->process();

// Creating a report object
$report = new StiReport();

// Loading a report by URL
// This method does not load the report object on the server side, it only generates the necessary JavaScript code
// The report will be loaded into a JavaScript object on the client side
$report->loadFile('../reports/SimpleList.mrt');

// Assigning a report object to the designer
$designer->report = $report;

// Displaying the visual part of the designer as a prepared HTML page
$designer->printHtml();
