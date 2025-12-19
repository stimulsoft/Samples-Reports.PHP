<?php

namespace Stimulsoft\Adapters;

use DateTime;
use PDO;
use PDOException;
use Stimulsoft\Events\StiConnectionEventArgs;
use Stimulsoft\StiBaseHandler;
use Stimulsoft\StiBaseResult;
use Stimulsoft\StiConnectionInfo;
use Stimulsoft\Enums\StiDatabaseType;
use Stimulsoft\StiDataResult;
use Stimulsoft\StiFileResult;
use Stimulsoft\StiFunctions;
use Stimulsoft\StiPath;

class StiDataAdapter
{

### Constants

    const UnknownError = "An unknown error has occurred.";


### Properties

    /** @var string Current version of the data adapter. */
    public $version = '2026.1.1';

    /** @var bool Sets the version matching check on the server and client sides. */
    public $checkVersion = false;

    /** @var StiBaseHandler */
    public $handler;

    /** @var StiDatabaseType|string The type of database processed by the data adapter. */
    protected $type;

    /** @var string The type of the current PHP data driver. */
    protected $driverType = 'Native';

    /** @var string The name of the current PHP data driver. */
    protected $driverName;

    /** @var string The connection string or URL for the current data source. */
    protected $connectionString;

    /** @var object|resource Link to the database connection driver. */
    protected $connectionLink;


### Methods

    protected function connect(): StiDataResult
    {
        return StiDataResult::getSuccess($this)->getDataAdapterResult($this);
    }

    protected function disconnect()
    {
        $this->connectionLink = null;
    }

    public function test(): StiDataResult
    {
        $result = $this->connect();
        if ($result->success)
            $this->disconnect();

        return $result;
    }

    public function process(): bool
    {
        return false;
    }

    public function getDataResult(?string $queryString, ?int $maxDataRows = null): StiDataResult
    {
        return StiDataResult::getSuccess()->getDataAdapterResult($this);
    }


### Helpers

    /**
     * @param StiDatabaseType|string $database [enum] The database type for which the command will be executed.
     * @param string|null $connectionString The connection string or URL for the current data source.
     */
    public static function getDataAdapter(string $database, ?string $connectionString)
    {
        switch ($database) {
            case StiDatabaseType::MySQL:
                return new StiMySqlAdapter($connectionString);

            case StiDatabaseType::MSSQL:
                return new StiMsSqlAdapter($connectionString);

            case StiDatabaseType::Firebird:
                return new StiFirebirdAdapter($connectionString);

            case StiDatabaseType::PostgreSQL:
                return new StiPostgreSqlAdapter($connectionString);

            case StiDatabaseType::Oracle:
                return new StiOracleAdapter($connectionString);

            case StiDatabaseType::ODBC:
                return new StiOdbcAdapter($connectionString);

            case StiDatabaseType::MongoDB:
                return new StiMongoDbAdapter($connectionString);

            case StiDatabaseType::XML:
                return new StiXmlAdapter($connectionString);

            case StiDatabaseType::JSON:
                return new StiJsonAdapter($connectionString);

            case StiDatabaseType::CSV:
                return new StiCsvAdapter($connectionString);
        }

        return null;
    }


### Constructor

    public function __construct(?string $connectionString)
    {
        $this->connectionString = StiFunctions::isNullOrEmpty($connectionString) ? null : trim($connectionString);
        $this->process();
    }
}