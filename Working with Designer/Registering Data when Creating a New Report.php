<?php
require_once '../vendor/autoload.php';

use Stimulsoft\Designer\StiDesigner;
use Stimulsoft\Report\StiReport;


// Creating a designer object and set the necessary javascript options
$designer = new StiDesigner();
$designer->javascript->useRelativeUrls = false;

// Defining designer events before processing
// It is allowed to assign a PHP function, or the name of a JavaScript function, or a JavaScript function as a string
// Also it is possible to add several functions of different types using the append() method
$designer->onCreateReport = 'onCreateReport';

// Processing the request and, if successful, immediately printing the result
$designer->process();

// Creating a report object
$report = new StiReport();

// Assigning a report object to the designer
$designer->report = $report;
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>Registering Data when Creating a New Report</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <?php
    // Rendering the necessary JavaScript code of the viewer
    $designer->javascript->renderHtml();
    ?>

    <script type="text/javascript">

        // The function will be called after the report is created before it is assigned to the designer
        function onCreateReport(args) {

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

            // Synchronizing data with the report template dictionary
            args.report.dictionary.synchronize();
        }
    </script>
</head>
<body>
<h2>Registering Data when Creating a New Report</h2>
<hr>
<?php
// Rendering the visual HTML part of the designer
$designer->renderHtml();
?>
</body>
</html>
