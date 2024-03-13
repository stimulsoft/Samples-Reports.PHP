<?php
require_once 'vendor/autoload.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Rendering a Report from Code</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <?php
    // Creating and configuring a JavaScript deployment object for the report generator
    $js = new \Stimulsoft\StiJavaScript(\Stimulsoft\StiComponentType::Report);

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

        // Creating the report object
        $report = new \Stimulsoft\Report\StiReport();

        // Loading a report by URL
        // This method does not load the report object on the server side, it only generates the necessary JavaScript code
        // The report will be loaded into a JavaScript object on the client side
        $report->loadFile('reports/SimpleList.mrt');

        // Calling the report build
        // This method does not render the report on the server side, it only generates the necessary JavaScript code
        // The report will be rendered using a JavaScript engine on the client side
        // If required, the specified JavaScript function will be called after building the report
        $report->render('afterRender');

        // Rendering the necessary JavaScript code and HTML part of the report generator
        $report->renderHtml();
        ?>

        function afterRender() {
            alert('Done!')
        }
    </script>
</head>
<body>
<h1>Rendering a Report from Code</h1>
</body>
</html>