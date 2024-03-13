<?php
require_once 'vendor/autoload.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>Localizing the Designer</title>
    <style>
        html, body {
            font-family: sans-serif;
        }
    </style>

    <?php
    // Creating and configuring a JavaScript deployment object for the designer
    $js = new \Stimulsoft\StiJavaScript(\Stimulsoft\StiComponentType::Designer);

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

        // Creating and configuring the designer options object
        $options = new \Stimulsoft\Designer\StiDesignerOptions();
        $options->appearance->fullScreenMode = true;

        // Defining the required interface localization
        // The list of available localizations can be obtained from the GitHub repository:
        // https://github.com/stimulsoft/Stimulsoft.Reports.Localization
        $options->localization = 'de.xml';

        // Additionally, it is possible to add optional localizations
        // They will be displayed in the localization menu in the designer panel
        $options->addLocalization('fr.xml');
        $options->addLocalization('es.xml');
        $options->addLocalization('pt.xml');

        // Creating the designer object with the necessary options
        $designer = new \Stimulsoft\Designer\StiDesigner($options);

        // Creating the report object
        $report = new \Stimulsoft\Report\StiReport();

        // Loading a report by URL
        // This method does not load the report object on the server side, it only generates the necessary JavaScript code
        // The report will be loaded into a JavaScript object on the client side
        $report->loadFile('reports/SimpleList.mrt');

        // Assigning a report object to the designer
        $designer->report = $report;
        ?>

        function onLoad() {
            <?php
            // Rendering the necessary JavaScript code and visual HTML part of the designer
            // The rendered code will be placed inside the specified HTML element
            $designer->renderHtml('designerContent');
            ?>
        }
    </script>
</head>
<body onload="onLoad();">
<div id="designerContent"></div>
</body>
</html>