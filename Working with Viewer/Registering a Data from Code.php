<?php
require_once '../vendor/autoload.php';

use Stimulsoft\Report\StiReport;
use Stimulsoft\Viewer\StiViewer;


// Creating a viewer object and set the necessary javascript options
$viewer = new StiViewer();
$viewer->javascript->relativePath = '../';

// Creating a report object
$report = new StiReport();

// Defining report events before processing
// It is allowed to assign a PHP function, or the name of a JavaScript function, or a JavaScript function as a string
// Also it is possible to add several functions of different types using the append() method
$report->onBeforeRender = 'onBeforeRender';

// Processing the request and, if successful, immediately printing the result
$viewer->process();

// Loading a report by URL
// This method does not load the report object on the server side, it only generates the necessary JavaScript code
// The report will be loaded into a JavaScript object on the client side
$report->loadFile('../reports/SimpleList.mrt');

// Assigning a report object to the viewer
$viewer->report = $report;
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>Registering a Data from Code</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <?php
    // Rendering the necessary JavaScript code of the viewer
    $viewer->javascript->renderHtml();
    ?>

    <script type="text/javascript">

        // This event will be triggered before the report is built
        function onBeforeRender(args) {

            // Creating new DataSet object
            let dataSet = new Stimulsoft.System.Data.DataSet("Demo");

            // Loading XSD schema file from specified URL to the DataSet object
            dataSet.readXmlSchemaFile("../data/Demo.xsd");

            // Loading XML data file from specified URL to the DataSet object
            dataSet.readXmlFile("../data/Demo.xml");

            // Loading JSON data file (instead of XML data file) from specified URL to the DataSet object
            //dataSet.readJsonFile("../data/Demo.json");

            // Removing all connections from the report template
            args.report.dictionary.databases.clear();

            // Registering DataSet object
            args.report.regData("Demo", "Demo", dataSet);
        }
    </script>
</head>
<body>
<h2>Registering a Data from Code</h2>
<hr>
<?php
// Rendering the visual HTML part of the viewer
$viewer->renderHtml();
?>
</body>
</html>