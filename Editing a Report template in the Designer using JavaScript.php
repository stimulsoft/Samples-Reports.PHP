<?php
require_once 'vendor/autoload.php';

use Stimulsoft\StiHandler;

?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Editing a Report template in the Designer</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <!-- https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_deployment.htm -->
    <script src="/vendor/stimulsoft/reports-php/public/scripts/stimulsoft.reports.js" type="text/javascript"></script>
    <script src="/vendor/stimulsoft/reports-php/public/scripts/stimulsoft.viewer.js" type="text/javascript"></script>
    <script src="/vendor/stimulsoft/reports-php/public/scripts/stimulsoft.designer.js" type="text/javascript"></script>
    <script src="/vendor/stimulsoft/reports-php/public/scripts/stimulsoft.blockly.editor.js" type="text/javascript"></script>

    <script type="text/javascript">
        <?php
        $handler = new StiHandler();
        $handler->renderHtml();
        ?>

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_designer_settings.htm */
        let options = new Stimulsoft.Designer.StiDesignerOptions();
        options.appearance.fullScreenMode = true;

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_designer_deployment.htm */
        let designer = new Stimulsoft.Designer.StiDesigner(options, "StiDesigner", false);

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_connecting_sql_data.htm */
        designer.onBeginProcessData = function (args, callback) {
            Stimulsoft.Helper.process(args, callback);
        }

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_viewer_showing_reports_and_dashboards.htm */
        let report = new Stimulsoft.Report.StiReport();
        report.loadFile("reports/SimpleList.mrt");
        designer.report = report;

        function onLoad() {
            designer.renderHtml("designerContent");
        }
    </script>
</head>
<body onload="onLoad();">
<div id="designerContent"></div>
</body>
</html>