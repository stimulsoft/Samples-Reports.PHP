<?php
require_once 'vendor/autoload.php';

use Stimulsoft\Events\StiDataEventArgs;
use Stimulsoft\Report\Enums\StiEngineType;
use Stimulsoft\Report\StiReport;


// Creating a report object
$report = new StiReport();

// Setting the server-side rendering mode
$report->engine = StiEngineType::ServerNodeJS;

// Defining viewer events before processing
// It is allowed to assign a PHP function, or the name of a JavaScript function, or a JavaScript function as a string
// Also it is possible to add several functions of different types using the append() method
$report->onBeginProcessData = function (StiDataEventArgs $args) {
    // In this event, it is possible to handle the data request, and replace the connection parameters

    // You can change the connection string
    // This example uses the 'Northwind' SQL database, it is located in the 'Data' folder
    // You need to import it and correct the connection string to your database
    if ($args->connection == 'MyConnectionName')
        $args->connectionString = 'Server=localhost; Database=northwind; UserId=root; Pwd=;';

    // You can change the SQL query
    if ($args->dataSource == 'MyDataSource')
        $args->queryString = 'SELECT * FROM MyTable';

    // You can change the SQL query parameters with the required values
    // For example: SELECT * FROM @Parameter1 WHERE Id = @Parameter2 AND Date > @Parameter3
    if ($args->dataSource == 'MyDataSourceWithParams') {
        $args->parameters['Parameter1']->value = 'TableName';
        $args->parameters['Parameter2']->value = 10;
        $args->parameters['Parameter3']->value = '2019-01-20';
    }
};

// Processing the request and, if successful, immediately printing the result
$report->process();

// Loading a report by URL
// This method loads a report file and stores it as a compressed string in an object
// The report will be loaded from this string into a JavaScript object when using Node.js
$report->loadFile('reports/SimpleListSQL.mrt');

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

use Stimulsoft\Export\Enums\StiExportFormat;
$message .= $report->exportDocument(StiExportFormat::Html);
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Rendering a Report with SQL from Code on the Server-Side</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>
</head>
<body>
<h2>Rendering a Report with SQL from Code on the Server-Side</h2>
<hr>
<?php
// Displaying the result message
echo $message;
?>
</body>
</html>
