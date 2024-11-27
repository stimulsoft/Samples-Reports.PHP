<?php
require_once '../vendor/autoload.php';

use Stimulsoft\StiLicense;
use Stimulsoft\Report\StiReport;


// Creating a report object and set the necessary javascript options
$report = new StiReport();
$report->javascript->useRelativeUrls = false;

// You can use one of the methods below to register your license key for all components
//StiLicense::setPrimaryKey('6vJhGtLLLz2GNviWmUTrhSqnO...');
//StiLicense::setPrimaryFile('license.key');

// You can use one of the methods below to register your license key only for the specified component
//$report->license->setKey('6vJhGtLLLz2GNviWmUTrhSqnO...');
//$report->license->setFile('license.key');

// Processing the request and, if successful, immediately printing the result
$report->process();

// Loading a report by URL
// This method does not load the report object on the server side, it only generates the necessary JavaScript code
// The report will be loaded into a JavaScript object on the client side
$report->loadFile('../reports/SimpleList.mrt');

// Calling the report build
// This method does not render the report on the server side, it only generates the necessary JavaScript code
// The report will be rendered using a JavaScript engine on the client side
$report->render();
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>How to activate the Product</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
        code {
            padding: 6px 8px;
            margin: 8px 0 0 0;
            font-size: 1.2em;
            background: #eee;
            border-radius: 6px;
            display: inline-block;
        }
    </style>

    <?php
    // Rendering the necessary JavaScript code of the report engine
    $report->javascript->renderHtml();
    ?>

    <script>
        // You can use one of the JavaScript methods below to register your license key
        //Stimulsoft.Base.StiLicense.loadFromString('6vJhGtLLLz2GNviWmUTrhSqnO...');
        //Stimulsoft.Base.StiLicense.loadFromFile('license.key');
    </script>
</head>
<body>
<h2>How to Activate the Product</h2>
<hr>
<?php
// Rendering the HTML part of the report engine
$report->renderHtml();
?>
<br>
The 30-day trial version of the product does not contain any restrictions, except for the Trial watermark on the report pages, and reminders about using the Trial
version.<br>
After purchasing the product, you can download the license key from your <a href="https://devs.stimulsoft.com/" target="_blank">personal account</a> on the
website.<br><br>

You can activate the product in several ways:
<ul>
    <li>Using one of the static methods below to register your license for all components:<br>
        <code>StiLicense::setPrimaryKey('6vJhGtLLLz2GNviWmUTrhSqnO...');</code><br>
        <code>StiLicense::setPrimaryFile('license.key');</code><br><br><br>
    </li>
    <li>Using one of the methods below to register your license only for the specified component:<br>
        <code>$report->license->setKey('6vJhGtLLLz2GNviWmUTrhSqnO...');</code><br>
        <code>$report->license->setFile('license.key');</code><br><br><br>
    </li>
    <li>Using one of the functions below to register your license from the JavaScript code:<br>
        <code>Stimulsoft.Base.StiLicense.loadFromString('6vJhGtLLLz2GNviWmUTrhSqnO...');</code><br>
        <code>Stimulsoft.Base.StiLicense.loadFromFile('license.key');</code><br><br>
    </li>
</ul><br>

For more details, please see the
<a href="https://www.stimulsoft.com/en/documentation/online/programming-manual/reports_and_dashboards_for_php_engine_activation.htm"
   target="_blank">documentation</a>.

</body>