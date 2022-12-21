<?php

namespace Stimulsoft;

class StiDataEventArgs extends StiEventArgs
{
    /** @var StiDataCommand The current command for the data adapter. */
    public $command;

    /** @var StiDatabaseType The database type for which the command will be executed. */
    public $database;

    /** @var string The name of the current database connection. */
    public $connection;

    /** @var string The name of the current data source. */
    public $dataSource;

    /** @var string The connection string for the current data source. */
    public $connectionString;

    /** @var string The SQL query that will be executed to get the data array of the current data source. */
    public $queryString;

    /** @var array A set of parameters for the current SQL query. */
    public $parameters;
}