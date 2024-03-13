<?php
require_once 'vendor/autoload.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Exporting a Report from Code</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <?php
    // Creating and configuring a JavaScript deployment object for the report generator
    $js = new \Stimulsoft\StiJavaScript(\Stimulsoft\StiComponentType::Report);

    // Rendering the JavaScript code required for the component to work
    $js->renderHtml();
    ?>

    <script type="text/javascript">
        <?php
        // Creating and configuring an event handler object
        // By default, the event handler sends all requests to the 'handler.php' file
        $handler = new \Stimulsoft\StiHandler();

        // Rendering the JavaScript code necessary for the event handler to work
        $handler->renderHtml();

        // Creating the report object
        $report = new \Stimulsoft\Report\StiReport();

        // Loading a report by URL
        // This method does not load the report object on the server side, it only generates the necessary JavaScript code
        // The report will be loaded into a JavaScript object on the client side
        $report->loadFile('reports/SimpleList.mrt');

        // Calling the report build
        // This method does not render the report on the server side, it only generates the necessary JavaScript code
        // The report will be rendered using a JavaScript engine on the client side
        $report->render();
        ?>

        function exportToPdf() {
            <?php
            // Calling the report export to the PDF format
            // This method does not export the report on the server side, it only generates the necessary JavaScript code
            // The report will be exported using a JavaScript engine on the client side
            $report->exportDocument(\Stimulsoft\StiExportFormat::Pdf);
            $report->renderHtml();
            ?>
        }

        function exportToExcel() {
            <?php
            // Calling the report export to the Excel format
            // This method does not export the report on the server side, it only generates the necessary JavaScript code
            // The report will be exported using a JavaScript engine on the client side
            $report->exportDocument(\Stimulsoft\StiExportFormat::Excel2007);
            $report->renderHtml();
            ?>
        }

        function exportToHtml() {
            <?php
            // Calling the report export to the HTML format
            // This method does not export the report on the server side, it only generates the necessary JavaScript code
            // The report will be exported using a JavaScript engine on the client side
            $report->exportDocument(\Stimulsoft\StiExportFormat::Html);
            $report->renderHtml();
            ?>
        }
    </script>
</head>
<body>
<h1>Exporting a Report from Code</h1>
<hr/>
<br/>
<button onclick="exportToPdf();">Export Report to PDF</button>
<br/><br/>
<button onclick="exportToExcel();">Export Report to Excel</button>
<br/><br/>
<button onclick="exportToHtml();">Export Report to HTML</button>
</body>
</html>