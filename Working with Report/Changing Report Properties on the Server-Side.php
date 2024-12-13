<?php
require_once '../vendor/autoload.php';

use Stimulsoft\Report\StiReport;


// Creating a report object and set the necessary javascript options
$report = new StiReport();
$report->javascript->relativePath = '../';

// Defining report events before processing
// It is allowed to assign a PHP function, or the name of a JavaScript function, or a JavaScript function as a string
// Also it is possible to add several functions of different types using the append() method
$report->onAfterRender = 'onAfterRender';

// Processing the request and, if successful, immediately printing the result
$report->process();

// Loading a report by URL
$reportJsonString = file_get_contents('../reports/Variables.mrt');

// Converting a report into an object and changing the necessary parameters
$reportJson = json_decode($reportJsonString);
$reportJson->ReportAlias = 'Report Alias from Server-Side';
$reportJson->CalculationMode = 'Interpretation';
$reportJson->Dictionary->Variables->{'0'}->Value = 'Value from Server-Side';

// Loading a report from a JSON object
// This method does not load the report object on the server side, it only generates the necessary JavaScript code
// The report will be loaded into a JavaScript object on the client side
$report->load($reportJson);

// Calling the report build
// This method does not render the report on the server side, it only generates the necessary JavaScript code
// The report will be rendered using a JavaScript engine on the client side
$report->render();
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>Changing Report Properties on the Server-Side</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <?php
    // Rendering the necessary JavaScript code of the report engine
    $report->javascript->renderHtml();
    ?>

    <script>
        // The function will be called after building the report
        function onAfterRender(args) {

            // Displaying a message with the number of built report pages
            document.getElementById("message").innerText =  "The report rendering is completed. Pages: " + args.report.renderedPages.count;

            // Saving the report as a JSON string
            document.getElementById("reportJson").innerText = args.report.saveDocumentToJsonString();
        }
    </script>
</head>
<body>
<h2>Changing Report Properties on the Server-Side</h2>
<hr>
<?php
// Rendering the HTML part of the report engine
$report->renderHtml();
?>
<div id="message"></div>
<pre id="reportJson"></pre>
</body>
</html>