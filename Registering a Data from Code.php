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
    /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_deployment.htm */
    $js = new \Stimulsoft\StiJavaScript(\Stimulsoft\StiComponentType::Viewer);
    $js->renderHtml();
    ?>

    <script type="text/javascript">
        <?php
        $handler = new \Stimulsoft\StiHandler();
        //$handler->license->setKey('6vJhGtLLLz2GNviWmUTrhSqnO...');
        //$handler->license->setFile('license.key');
        $handler->renderHtml();

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_settings.htm */
        $options = new \Stimulsoft\Viewer\StiViewerOptions();
        $options->appearance->fullScreenMode = true;
        $options->appearance->scrollbarsMode = true;
        $options->height = '600px'; // Height for non-fullscreen mode

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_deployment.htm */
        $viewer = new \Stimulsoft\Viewer\StiViewer($options);

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_connecting_data_files.htm */
        $viewer->onBeginProcessData = 'onBeginProcessData';

        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_web_designer_creating_editing_report.htm */
        $report = new \Stimulsoft\Report\StiReport();
        $report->loadFile('reports/SimpleList.mrt');
        $viewer->report = $report;
        ?>

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
            $viewer->renderHtml('viewerContent');
            ?>
        }
    </script>
</head>
<body onload="onLoad();">
<div id="viewerContent"></div>
</body>
</html>