<?php
require_once 'vendor/autoload.php';

use Stimulsoft\Enums\StiComponentType;
use Stimulsoft\StiHandler;
use Stimulsoft\StiJavaScript;

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>How to activate the Product</title>
    <style>
        html, body {
            font-family: sans-serif;
        }

        code {
            padding: 6px 8px;
            margin: 10px 0;
            font-size: 1.2em;
            background: #eee;
            border-radius: 6px;
            display: inline-block;
        }
    </style>

    <?php
    /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_deployment.htm */
    $helper = new StiJavaScript(StiComponentType::Engine);
    $helper->renderHtml();
    ?>

    <script type="text/javascript">
        <?php
        /** https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_and_dashboards_for_php_engine_activation.htm */
        $handler = new StiHandler();
        //$handler->license->setKey('6vJhGtLLLz2GNviWmUTrhSqnO...');
        //$handler->license->setFile('license.key');
        $handler->renderHtml();
        ?>
    </script>
</head>
<body>
<h1>How to activate the Product</h1>
<hr/>
<br/>
The 30-day trial version of the product does not contain any restrictions, except for the Trial watermark on the report pages, and reminders about using the Trial
version.<br/>
After purchasing the product, you can download the license key from your <a href="https://devs.stimulsoft.com/" target="_blank">personal account</a> on the
website.<br/><br/>

You can activate the product in several ways:
<ul>
    <li>Set the key as a Base64 string for the StiHandler() object:<br/>
        <code>$handler = new StiHandler();<br/>$handler->license->setKey('6vJhGtLLLz2GNviWmUTrhSqnO...');</code><br/><br/></li>
    <li>Set the key as a license file for the StiHandler() object:<br/>
        <code>$handler = new StiHandler();<br/>$handler->license->setFile('license.key');</code><br/><br/></li>
    <li>Using the special line of JavaScript code with a key as a Base64 string:<br/>
        <code>Stimulsoft.Base.StiLicense.Key = '6vJhGtLLLz2GNviWmUTrhSqnO...';</code><br/><br/></li>
</ul>

<br/>For more details, please see the
<a href="https://www.stimulsoft.com/en/documentation/online/programming-manual/reports_and_dashboards_for_php_engine_activation.htm"
   target="_blank">documentation</a>.
</body>