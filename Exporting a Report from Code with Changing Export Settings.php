<?php
require_once 'vendor/autoload.php';

use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\Export\StiPdfExportSettings;
use Stimulsoft\Report\Enums\StiEngineType;
use Stimulsoft\Report\StiReport;


// Creating a report object
$report = new StiReport();

// Setting the server-side rendering mode
$report->engine = StiEngineType::ServerNodeJS;

// Processing the request and, if successful, immediately printing the result
$report->process();

// Loading a report by URL
// This method loads a report file and stores it as a compressed string in an object
// The report will be loaded from this string into a JavaScript object when using Node.js
$report->loadFile('reports/SimpleList.mrt');

// Building a report
// The report is not built using PHP code
// This method will prepare JavaScript code and pass it to Node.js, which will build a report and return the finished document
$result = $report->render();

if ($result) {
    // After successfully building a report, creating a settings object and changing the necessary ones
    $settings = new StiPdfExportSettings();
    $settings->creatorString = 'My Company Name';
    $settings->keywordsString = 'SimpleList PHP Report Export';
    $settings->embeddedFonts = false;

    // Calling the document export method with settings. Export is also performed using Node.js engine
    // This method will save the exported report as a file
    $result = $report->exportDocument(StiExportFormat::Pdf, $settings, false, 'reports/SimpleList.pdf');

    // Creating a message about the export result
    $message = $result ? 'Exporting the report to PDF was successful.' : $report->nodejs->error;
}
else {
    // If there is a build error, you can display the error text
    $message = $report->nodejs->error;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Exporting a Report from Code with Changing Export Settings</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>
</head>
<body>
<h2>Exporting a Report from Code with Changing Export Settings</h2>
<hr>
<?php
// Displaying the result message
echo $message;
?>
</html>