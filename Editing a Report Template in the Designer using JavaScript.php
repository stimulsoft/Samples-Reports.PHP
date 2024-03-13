<?php
require_once 'vendor/autoload.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Editing a Report Template in the Designer</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <!-- Adding JavaScript code required for the designer to work -->
    <script src="vendor/stimulsoft/reports-php/scripts/stimulsoft.reports.js" type="text/javascript"></script>
    <script src="vendor/stimulsoft/reports-php/scripts/stimulsoft.viewer.js" type="text/javascript"></script>
    <script src="vendor/stimulsoft/reports-php/scripts/stimulsoft.designer.js" type="text/javascript"></script>
    <script src="vendor/stimulsoft/reports-php/scripts/stimulsoft.blockly.editor.js" type="text/javascript"></script>

    <script type="text/javascript">
        <?php
        // Creating and configuring an event handler object
        // By default, the event handler sends all requests to the 'handler.php' file
        $handler = new \Stimulsoft\StiHandler();

        // Rendering the JavaScript code necessary for the event handler to work
        $handler->renderHtml();
        ?>

        // Creating and configuring the designer options object
        let options = new Stimulsoft.Designer.StiDesignerOptions();
        options.appearance.fullScreenMode = true;

        // Creating the designer object with the necessary options
        let designer = new Stimulsoft.Designer.StiDesigner(options, "StiDesigner", false);

        // Defining designer events
        // This event will be triggered when requesting data for a report
        // To process the result on the server-side, you need to call the JavaScript event handler in the event
        designer.onBeginProcessData = function (args, callback) {
            Stimulsoft.Helper.process(args, callback);
        }

        // Creating the report object
        let report = new Stimulsoft.Report.StiReport();

        // Loading a report by URL
        report.loadFile("reports/SimpleList.mrt");

        // Assigning a report object to the designer
        designer.report = report;

        function onLoad() {
            // Rendering the necessary JavaScript code and visual HTML part of the designer
            // The rendered code will be placed inside the specified HTML element
            designer.renderHtml("designerContent");
        }
    </script>
</head>
<body onload="onLoad();">
<div id="designerContent"></div>
</body>
</html>