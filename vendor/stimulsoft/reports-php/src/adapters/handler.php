<?php

use Stimulsoft\StiBaseHandler;
use Stimulsoft\Events\StiDataEventArgs;

// Event handler classes and functions
require_once 'enums\StiBaseEventType.php';
require_once 'enums\StiDataCommand.php';
require_once 'enums\StiDatabaseType.php';
require_once 'events\StiEvent.php';
require_once 'events\StiEventArgs.php';
require_once 'events\StiConnectionEventArgs.php';
require_once 'events\StiDataEventArgs.php';
require_once 'classes\StiConnectionInfo.php';
require_once 'classes\StiFunctions.php';
require_once 'classes\StiBaseRequest.php';
require_once 'classes\StiBaseResult.php';
require_once 'classes\StiDataResult.php';
require_once 'classes\StiBaseResponse.php';
require_once 'classes\StiBaseHandler.php';
require_once 'classes\StiParameter.php';

// Data adapters for supported database types
require_once 'StiDataAdapter.php';
require_once 'StiFirebirdAdapter.php';
require_once 'StiMongoDbAdapter.php';
require_once 'StiMsSqlAdapter.php';
require_once 'StiMySqlAdapter.php';
require_once 'StiOdbcAdapter.php';
require_once 'StiOracleAdapter.php';
require_once 'StiPostgreSqlAdapter.php';

// You can configure the security level as you required.
// By default is to allow any requests from any domains.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Engaged-Auth-Token');
header('Cache-Control: no-cache');

// Processing database connection parameters.
$onBeginProcessData = function (StiDataEventArgs $args) {
    
};

$handler = new StiBaseHandler();
$handler->onBeginProcessData->append($onBeginProcessData);
$handler->process();