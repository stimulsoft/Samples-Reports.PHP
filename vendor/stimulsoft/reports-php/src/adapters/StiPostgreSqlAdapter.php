<?php

namespace Stimulsoft\Adapters;

use Stimulsoft\Enums\StiDatabaseType;
use Stimulsoft\Events\StiConnectionEventArgs;
use Stimulsoft\StiDataResult;

class StiPostgreSqlAdapter extends StiDataAdapter
{

### Constants

    const DriverNotFound = 'PostgreSQL driver not found. Please configure your PHP server to work with PostgreSQL.';


### Properties

    public $version = '2025.1.2';
    public $checkVersion = true;

    protected $type = StiDatabaseType::PostgreSQL;
    protected $driverName = 'pgsql';


### Methods

    protected function getLastErrorResult(): StiDataResult
    {
        if ($this->driverType == 'PDO')
            return parent::getLastErrorResult();

        $error = pg_last_error();
        $message = $error ?: self::UnknownError;

        return StiDataResult::getError($message)->getDataAdapterResult($this);
    }

    protected function connect(): StiDataResult
    {
        if ($this->driverType == 'PDO')
            return parent::connect();

        if (!function_exists('pg_connect'))
            return StiDataResult::getError(self::DriverNotFound)->getDataAdapterResult($this);

        $args = new StiConnectionEventArgs($this->type, $this->driverName, $this->connectionInfo);
        $this->handler->onDatabaseConnect->call($args);

        if ($args->link !== null)
            $this->connectionLink = $args->link;
        else {
            $connectionString =
                "host='" . $this->connectionInfo->host . "' port='" . $this->connectionInfo->port . "' dbname='" . $this->connectionInfo->database .
                "' user='" . $this->connectionInfo->userId . "' password='" . $this->connectionInfo->password .
                "' options='--client_encoding=" . $this->connectionInfo->charset . "'";
            $this->connectionLink = pg_connect($connectionString);
        }

        if (!$this->connectionLink)
            return $this->getLastErrorResult();

        return StiDataResult::getSuccess()->getDataAdapterResult($this);
    }

    protected function disconnect()
    {
        if ($this->driverType == 'PDO')
            parent::disconnect();
        else if ($this->connectionLink) {
            pg_close($this->connectionLink);
            $this->connectionLink = null;
        }
    }

    public function process(): bool
    {
        if (parent::process()) return true;

        $this->connectionInfo->port = 5432;
        $this->connectionInfo->charset = 'utf8';

        $parameterNames = array(
            'host' => ['server', 'host', 'location'],
            'port' => ['port'],
            'database' => ['database', 'data source', 'dbname'],
            'userId' => ['uid', 'user', 'username', 'userid', 'user id'],
            'password' => ['pwd', 'password'],
            'charset' => ['charset']
        );

        return $this->processParameters($parameterNames);
    }

    protected function getType($meta): string
    {
        $type = strtolower($this->driverType == 'PDO' ? $meta['native_type'] : $meta);
        if (substr($type, 0, 1) == '_')
            $type = 'array';

        switch ($type) {
            case "int":
            case 'int2':
            case 'int4':
            case 'int8':
            case "smallint":
            case "bigint":
            case "tinyint":
            case "integer":
            case 'numeric':
            case "uniqueidentifier":
                return 'int';

            case "float":
            case 'float4':
            case 'float8':
            case "real":
            case "double":
            case "decimal":
            case "smallmoney":
            case "money":
                return 'number';

            case 'bool':
            case "boolean":
                return 'boolean';

            case "abstime":
            case "date":
            case "datetime":
            case "smalldatetime":
            case "timestamp":
                return 'datetime';

            case 'timetz':
            case 'timestamptz':
                return 'datetimeoffset';

            case 'time':
                return 'time';

            case 'bytea':
            case 'array':
                return 'array';
        }

        return 'string';
    }

    protected function getValue($type, $value)
    {
        if (is_null($value) || strlen($value) == 0)
            return null;

        switch ($type) {
            case 'array':
                return base64_encode($value);

            case 'datetime':
                $timestamp = strtotime($value);
                $format = date("Y-m-d\TH:i:s.v", $timestamp);
                if (strpos($format, '.v') > 0) $format = date("Y-m-d\TH:i:s.000", $timestamp);
                return $format;

            case 'datetimeoffset':
                if (strlen($value) <= 15) {
                    $offset = substr($value, strpos($value, '+'));
                    if (strlen($offset) == 3) $offset = $offset . ':00';
                    $value = substr($value, 0, strpos($value, '+'));
                    $value = '0001-01-01 ' . $value;
                    $timestamp = strtotime($value);
                    $format = date("Y-m-d\TH:i:s.v", $timestamp);
                    if (strpos($format, '.v') > 0) $format = date("Y-m-d\TH:i:s.000", $timestamp);
                    return $format . $offset;
                }

                $timestamp = strtotime($value);
                $format = gmdate("Y-m-d\TH:i:s.v\Z", $timestamp);
                if (strpos($format, '.v') > 0) $format = gmdate("Y-m-d\TH:i:s.000\Z", $timestamp);
                return $format;

            case 'time':
                $timestamp = strtotime($value);
                $format = date("H:i:s.v", $timestamp);
                if (strpos($format, '.v') > 0) $format = date("H:i:s.000", $timestamp);
                return $format;
        }

        return $value;
    }

    public function makeQuery($procedure, $parameters): string
    {
        $paramsString = parent::makeQuery($procedure, $parameters);
        return "CALL $procedure ($paramsString)";
    }

    protected function executeNative($queryString, $maxDataRows, $result): StiDataResult
    {
        $query = pg_query($this->connectionLink, $queryString);
        if (!$query)
            return $this->getLastErrorResult();

        $result->count = pg_num_fields($query);

        for ($i = 0; $i < $result->count; $i++) {
            $result->columns[] = pg_field_name($query, $i);
            $type = pg_field_type($query, $i);
            $result->types[] = $this->getType($type);
        }

        while ($rowItem = pg_fetch_assoc($query)) {
            $row = [];

            foreach ($rowItem as $value) {
                $type = count($result->types) >= count($row) + 1 ? $result->types[count($row)] : 'string';
                $row[] = $this->getValue($type, $value);
            }

            $result->rows[] = $row;

            if (count($result->rows) === $maxDataRows)
                break;
        }

        return $result;
    }
}