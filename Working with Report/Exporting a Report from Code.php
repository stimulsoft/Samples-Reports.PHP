<?php
require_once '../vendor/autoload.php';

use Stimulsoft\Enums\StiHtmlMode;
use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\Report\StiReport;


// Creating a report object and set the necessary javascript options
$report = new StiReport();
$report->javascript->useRelativeUrls = false;

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
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>Exporting a Report from Code</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <?php
    // Rendering the necessary JavaScript code of the report engine
    $report->javascript->renderHtml();
    ?>

    <script type="text/javascript">

        function exportToPdf() {
            <?php
            // Calling the report export to the PDF format
            // This method does not export the report on the server side, it only generates the necessary JavaScript code
            // The report will be exported using a JavaScript engine on the client side
            $report->exportDocument(StiExportFormat::Pdf);

            // Rendering only the JavaScript code of the report engine
            echo $report->getHtml(StiHtmlMode::Scripts);
            ?>
        }

        function exportToExcel() {
            <?php
            // Calling the report export to the Excel format
            // This method does not export the report on the server side, it only generates the necessary JavaScript code
            // The report will be exported using a JavaScript engine on the client side
            $report->exportDocument(StiExportFormat::Excel);

            // Rendering only the JavaScript code of the report engine
            echo $report->getHtml(StiHtmlMode::Scripts);
            ?>
        }

        function exportToHtml() {
            <?php
            // Calling the report export to the HTML format
            // This method does not export the report on the server side, it only generates the necessary JavaScript code
            // The report will be exported using a JavaScript engine on the client side
            $report->exportDocument(StiExportFormat::Html);

            // Rendering only the JavaScript code of the report engine
            echo $report->getHtml(StiHtmlMode::Scripts);
            ?>
        }
    </script>
</head>
<body>
<h2>Exporting a Report from Code</h2>
<hr>
<button onclick="exportToPdf();">Export Report to PDF</button>
<br><br>
<button onclick="exportToExcel();">Export Report to Excel</button>
<br><br>
<button onclick="exportToHtml();">Export Report to HTML</button>
</body>
</html>