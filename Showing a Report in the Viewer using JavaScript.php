<?php
require_once 'vendor/autoload.php';

use Stimulsoft\StiHandler;

?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Showing a Report in the Viewer using JavaScript</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <!-- https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_deployment.htm -->
    <script src="/vendor/stimulsoft/reports-php/scripts/stimulsoft.reports.js" type="text/javascript"></script>
    <script src="/vendor/stimulsoft/reports-php/scripts/stimulsoft.viewer.js" type="text/javascript"></script>

    <script type="text/javascript">
        <?php
        $handler = new StiHandler();
        $handler->renderHtml();
        ?>

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_activation.htm */
        //Stimulsoft.Base.StiLicense.Key = '6vJhGtLLLz2GNviWmUTrhSqnO...';
        //Stimulsoft.Base.StiLicense.loadFromFile('license.key');

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_settings.htm */
        let options = new Stimulsoft.Viewer.StiViewerOptions();
        options.appearance.fullScreenMode = true;
        options.appearance.scrollbarsMode = true;
        options.height = "600px"; // Height for non-fullscreen mode

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_deployment.htm */
        let viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_connecting_sql_data.htm */
        viewer.onBeginProcessData = function (args, callback) {
            Stimulsoft.Helper.process(args, callback);
        }

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_viewer_showing_reports_and_dashboards.htm */
        let report = new Stimulsoft.Report.StiReport();
        report.loadFile("reports/SimpleList.mrt");
        viewer.report = report;

        function onLoad() {
            viewer.renderHtml("viewerContent");
        }
    </script>
</head>
<body onload="onLoad();">
<div id="viewerContent"></div>
</body>
</html>