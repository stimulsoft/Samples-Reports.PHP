<?php
require_once 'vendor/autoload.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Registering a Data from Code</title>
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
        $options->appearance->scrollbarsMode = true;

        // Setting the height of the viewer for non-fullscreen mode
        $options->height = '600px';

        // Creating the viewer object with the necessary options
        $viewer = new \Stimulsoft\Viewer\StiViewer($options);

        // Defining viewer events
        // When assigning a function name as a string, it will be called on the JavaScript client side
        $viewer->onBeginProcessData = 'onBeginProcessData';

        // Creating the report object
        $report = new \Stimulsoft\Report\StiReport();

        // Loading a report by URL
        // This method does not load the report object on the server side, it only generates the necessary JavaScript code
        // The report will be loaded into a JavaScript object on the client side
        $report->loadFile('reports/SimpleList.mrt');

        // Assigning a report object to the viewer
        $viewer->report = $report;
        ?>

        // This event will be triggered when requesting data for a report
        function onBeginProcessData(args) {
            // Creating new DataSet object
            let dataSet = new Stimulsoft.System.Data.DataSet("Demo");

            // Loading XSD schema file from specified URL to the DataSet object
            dataSet.readXmlSchemaFile("data/Demo.xsd");

            // Loading XML data file from specified URL to the DataSet object
            dataSet.readXmlFile("data/Demo.xml");

            // Loading JSON data file (instead of XML data file) from specified URL to the DataSet object
            //dataSet.readJsonFile("../data/Demo.json");

            // Removing all connections from the report template
            args.report.dictionary.databases.clear();

            // Registering DataSet object
            args.report.regData("Demo", "Demo", dataSet);
        }

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