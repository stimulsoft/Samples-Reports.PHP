<?php

namespace Stimulsoft\Events;

use Stimulsoft\Enums\StiDatabaseType;
use Stimulsoft\StiConnectionInfo;

class StiConnectionEventArgs extends StiEventArgs
{
    /** @var StiDatabaseType|string [enum] The type of the current database connection. */
    public $database;

    /** @var string Driver used for connection. */
    public $driver;

    /** @var StiConnectionInfo Information about the current connection. */
    public $info;

    /** @var object Database connection identifier. */
    public $link;


### Constructor

    public function __construct($database, $driver, $info)
    {
        parent::__construct();

        $this->database = $database;
        $this->driver = $driver;
        $this->info = $info;
    }
}