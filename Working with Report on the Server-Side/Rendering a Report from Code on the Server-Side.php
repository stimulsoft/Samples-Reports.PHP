<?php
require_once '../vendor/autoload.php';

use Stimulsoft\Report\Enums\StiEngineType;
use Stimulsoft\Report\StiReport;


// Changing the working directory one level up, this is necessary because the examples are in a subdirectory
chdir('..');

// Creating a report object
$report = new StiReport();

// Setting the server-side rendering mode
$report->engine = StiEngineType::ServerNodeJS;

// Processing the request and, if successful, immediately printing the result
$report->process();

// Loading a report by URL
// This method loads a report file and stores it as a compressed string in an object
// The report will be loaded from this string into a JavaScript object when using Node.js
$report->loadFile('reports/SimpleList.mrt', true);

// Building a report
// The report is not built using PHP code
// This method will prepare JavaScript code and pass it to Node.js, which will build a report and return the finished document
$result = $report->render();

if ($result) {
    // After successfully building a report, the finished document can be saved as a string or to a file
    $document = $report->saveDocument();
    // $result = $report->saveDocument('reports/SimpleList.mdc');

    $bytes = strlen($document);
    $message = "The finished document takes $bytes bytes.";
}
else {
    // If there is a build error, you can display the error text
    $message = $report->nodejs->error;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>Rendering a Report from Code on the Server-Side</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>
</head>
<body>
<h2>Rendering a Report from Code on the Server-Side</h2>
<hr>
<?php
// Displaying the result message
echo $message;
?>
</body>
</html>
