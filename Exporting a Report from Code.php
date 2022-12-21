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
    /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_designer_deployment.htm */
    $js = new \Stimulsoft\StiJavaScript(\Stimulsoft\StiComponentType::Report);
    $js->renderHtml();
    ?>

    <script type="text/javascript">
        <?php
        $handler = new \Stimulsoft\StiHandler();
        //$handler->license->setKey('6vJhGtLLLz2GNviWmUTrhSqnO...');
        //$handler->license->setFile('license.key');
        $handler->renderHtml();

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_designer_creating_editing_report.htm */
        $report = new \Stimulsoft\Report\StiReport();
        $report->loadFile('reports/SimpleList.mrt');
        $report->render();
        ?>

        function exportToPdf() {
            <?php
            /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_export_from_code.htm */
            $report->exportDocument(\Stimulsoft\StiExportFormat::Pdf);
            $report->renderHtml();
            ?>
        }

        function exportToExcel() {
            <?php
            /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_export_from_code.htm */
            $report->exportDocument(\Stimulsoft\StiExportFormat::Excel2007);
            $report->renderHtml();
            ?>
        }

        function exportToHtml() {
            <?php
            /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_export_from_code.htm */
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