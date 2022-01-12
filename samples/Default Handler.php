<?php
require_once '../stimulsoft/helper.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token');
header('Cache-Control: no-cache');

// Creating the request handler
$handler = new StiHandler();

// Registering the error handlers
$handler->registerErrorHandlers();

// Processing the request from a client-side
$handler->process();