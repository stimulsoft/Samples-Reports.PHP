<?php
require_once 'vendor/autoload.php';

use Stimulsoft\Enums\StiComponentType;
use Stimulsoft\Enums\StiExportFormat;
use Stimulsoft\Report\StiReport;
use Stimulsoft\StiHandler;
use Stimulsoft\StiJavaScript;

?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Rendering a Report from Code</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <?php
    /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_designer_deployment.htm */
    $js = new StiJavaScript(StiComponentType::Report);
    $js->renderHtml();
    ?>

    <script type="text/javascript">
        <?php
        $handler = new StiHandler();
        //$handler->license->setKey('6vJhGtLLLz2GNviWmUTrhSqnO...');
        //$handler->license->setFile('license.key');
        $handler->renderHtml();

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_designer_creating_editing_report.htm */
        $report = new StiReport();
        $report->loadFile('reports/SimpleList.mrt');
        $report->render('afterRender');
        $report->renderHtml();
        ?>

        function afterRender() {
            alert('Done!')
        }
    </script>
</head>
<body>
<h1>Rendering a Report from Code</h1>
</body>
</html>