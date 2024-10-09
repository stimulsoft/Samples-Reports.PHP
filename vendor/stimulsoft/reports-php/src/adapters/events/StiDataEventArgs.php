<?php

namespace Stimulsoft\Events;

use Stimulsoft\Enums\StiDatabaseType;
use Stimulsoft\Enums\StiDataCommand;
use Stimulsoft\StiDataResult;

class StiDataEventArgs extends StiEventArgs
{
    /** @var StiDataCommand [enum] The current command for the data adapter. */
    public $command = null;

    /** @var StiDatabaseType [enum] The database type for which the command will be executed. */
    public $database = null;

    /** @var string The name of the current database connection. */
    public $connection = null;

    /** @var string The name of the current data source. */
    public $dataSource = null;

    /** @var string The connection string for the current data source. */
    public $connectionString = null;

    /** @var string The SQL query that will be executed to get the data array of the current data source. */
    public $queryString = null;

    /** @var int The maximum number of data rows. The value is taken from the designer's options for the dashboard in design mode. */
    public $maxDataRows = null;

    /** @var array A set of parameters for the current SQL query. */
    public $parameters = null;

    /** @var StiDataResult The result of executing an event handler request. */
    public $result = null;
}