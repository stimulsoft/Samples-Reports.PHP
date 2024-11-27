<?php
require_once '../vendor/autoload.php';

use Stimulsoft\StiNodeJs;


// Changing the working directory one level up, this is necessary because the examples are in a subdirectory
chdir('..');

// Creating a Node.js object
$nodejs = new StiNodeJs();

// Setting the path to the executable files of the already installed Node.js
//$nodejs->binDirectory = 'C:\\Program Files\\nodejs';
//$nodejs->binDirectory = '/usr/bin/nodejs';

// Setting the path to the working directory where Node.js scripts will be running
// By default, the current PHP script working directory is used
//$nodejs->workingDirectory = __DIR__;

// Installing the Node.js package from the official website, may take some time
// If the installation fails, the function will return false
$result = $nodejs->installNodeJS();

// Installing or updating product packages to the current version, may take some time
if ($result)
    $result = $nodejs->updatePackages();

// Installation status or error text from Node.js engine
$message = $result ? 'The installation was successful.' : $nodejs->error;
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <title>Configuring and Installing Node.js</title>
    <style>
        html, body {
        font-family: sans-serif;
    }
    </style>
</head>
<body>
<h2>Configuring and Installing Node.js</h2>
<hr>
<?php
// Printing a result text message
echo $message;
?>
</body>
</html>