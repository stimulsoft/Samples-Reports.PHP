<?php
require_once '../vendor/autoload.php';

use Stimulsoft\Designer\StiDesigner;
use Stimulsoft\Report\StiReport;


// Creating a designer object and set the necessary javascript options
$designer = new StiDesigner();
$designer->javascript->useRelativeUrls = false;

// Processing the request and, if successful, immediately printing the result
$designer->process();

// Creating a report object
$report = new StiReport();

// Loading a report by URL
// This method does not load the report object on the server side, it only generates the necessary JavaScript code
// The report will be loaded into a JavaScript object on the client side
$report->loadFile('../reports/SimpleList.mrt');

// Assigning a report object to the designer
$designer->report = $report;
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>Showing a Report in the Designer in an HTML template</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <?php
    // Rendering the necessary JavaScript code of the designer
    $designer->javascript->renderHtml();
    ?>
</head>
<body>
<h2>Showing a Report in the Designer in an HTML template</h2>
<hr>
<?php
// Rendering the visual HTML part of the designer
$designer->renderHtml();
?>
</body>
</html>