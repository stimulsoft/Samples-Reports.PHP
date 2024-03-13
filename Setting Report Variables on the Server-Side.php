<?php
require_once 'vendor/autoload.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Setting Report Variables on the Server-Side</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <?php
    // Creating and configuring a JavaScript deployment object for the viewer
    $js = new \Stimulsoft\StiJavaScript(\Stimulsoft\StiComponentType::Viewer);

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

        // Creating and configuring the viewer options object
        $options = new \Stimulsoft\Viewer\StiViewerOptions();
        $options->appearance->fullScreenMode = true;

        // Creating the viewer object with the necessary options
        $viewer = new \Stimulsoft\Viewer\StiViewer($options);

        // Defining viewer events
        // If set to true, this event will be passed to the server-side event handler
        // By default, all server-side events are located in the 'handler.php' file
        $viewer->onPrepareVariables = true;

        // Creating the report object
        $report = new \Stimulsoft\Report\StiReport();

        // Loading a report by URL
        // This method does not load the report object on the server side, it only generates the necessary JavaScript code
        // The report will be loaded into a JavaScript object on the client side
        $report->loadFile('reports/Variables.mrt');

        // Assigning a report object to the viewer
        $viewer->report = $report;
        ?>

        function onLoad() {
            <?php
            // Rendering the necessary JavaScript code and visual HTML part of the viewer
            // The rendered code will be placed inside the specified HTML element
            $viewer->renderHtml('viewerContent');
            ?>
        }
    </script>
</head>
<body onload="onLoad();">
<div id="viewerContent"></div>
</body>
</html>