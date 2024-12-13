<?php
require_once '../vendor/autoload.php';

use Stimulsoft\Events\StiDataEventArgs;
use Stimulsoft\StiHandler;


// Creating and configuring an event handler object
$handler = new StiHandler();

// By default, all requests are processed on the current page
// It is possible to specify a path that contains an event handler, for example the 'handler.php' file
// $handler = new StiHandler('handler.php');

// If necessary, define events before processing
// It is allowed to assign a PHP function, or the name of a JavaScript function, or a JavaScript function as a string
// Also it is possible to add several functions of different types using the append() method
$handler->onBeginProcessData = function (StiDataEventArgs $args) {

};

// Processing the request and, if successful, immediately printing the result
$handler->process();
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>Showing a Report in the Viewer in an HTML template using JavaScript</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <!-- Adding JavaScript code required for the viewer to work -->
    <script src="../vendor/stimulsoft/reports-php/scripts/stimulsoft.reports.js" type="text/javascript"></script>
    <script src="../vendor/stimulsoft/reports-php/scripts/stimulsoft.viewer.js" type="text/javascript"></script>
    
    <?php
    // Rendering the necessary JavaScript code of the handler
    $handler->renderHtml();
    ?>

    <script type="text/javascript">
    
        // Creating and configuring the viewer options object
        let options = new Stimulsoft.Viewer.StiViewerOptions();
        options.appearance.fullScreenMode = true;
        options.appearance.scrollbarsMode = true;

        // Setting the height of the viewer for non-fullscreen mode
        options.height = "600px";

        // Creating the viewer object with the necessary options
        let viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);

        // Defining viewer events
        // This event will be triggered when requesting data for a report
        // To process the result on the server-side, you need to call the JavaScript event handler in the event
        viewer.onBeginProcessData = function (args, callback) {
            Stimulsoft.handler.process(args, callback);
        }

        // Creating the report object
        let report = new Stimulsoft.Report.StiReport();

        // Loading a report by URL
        report.loadFile("../reports/SimpleList.mrt");

        // Assigning a report object to the viewer
        viewer.report = report;

        function onLoad() {
            // Rendering the necessary JavaScript code and visual HTML part of the viewer
            // The rendered code will be placed inside the specified HTML element
            viewer.renderHtml("viewerContent");
        }
    </script>
</head>
<body onload="onLoad();">
<h2>Showing a Report in the Viewer in an HTML template using JavaScript</h2>
<hr>
<div id="viewerContent"></div>
</body>
</html>