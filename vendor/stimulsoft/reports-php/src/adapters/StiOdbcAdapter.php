<?php

namespace Stimulsoft\Adapters;

use Stimulsoft\Enums\StiDatabaseType;
use Stimulsoft\Events\StiConnectionEventArgs;
use Stimulsoft\StiConnectionInfo;
use Stimulsoft\StiDataResult;

class StiOdbcAdapter extends StiDataAdapter
{

### Properties

    public $version = '2024.4.5';
    public $checkVersion = true;

    protected $type = StiDatabaseType::ODBC;


### Methods

    protected function getLastErrorResult(): StiDataResult
    {
        $code = odbc_error();
        $error = odbc_errormsg();
        $message = $error ?: self::UnknownError;
        if ($code != 0) $message = "[$code] $message";

        return StiDataResult::getError($message)->getDataAdapterResult($this);
    }

    protected function connect(): StiDataResult
    {
        $args = new StiConnectionEventArgs($this->type, $this->driverName, $this->connectionInfo);
        $this->handler->onDatabaseConnect->call($args);

        $this->connectionLink = $args->link !== null
            ? $args->link
            : odbc_connect($this->connectionInfo->dsn, $this->connectionInfo->userId, $this->connectionInfo->password);

        if (!$this->connectionLink)
            return $this->getLastErrorResult();

        return StiDataResult::getSuccess()->getDataAdapterResult($this);
    }

    protected function disconnect()
    {
        if ($this->connectionLink) {
            odbc_close($this->connectionLink);
            $this->connectionLink = null;
        }
    }

    public function process(): bool
    {
        $this->connectionInfo = new StiConnectionInfo();

        $parameterNames = array(
            'userId' => ['uid', 'user', 'username', 'userid', 'user id'],
            'password' => ['pwd', 'password']
        );

        return $this->processParameters($parameterNames);
    }

    protected function getType($meta): string
    {
        $type = strtolower($meta);
        switch ($type) {
            case 'short':
            case 'int':
            case 'int2':
            case 'int4':
            case 'int8':
            case 'int24':
            case 'integer':
            case 'long':
            case 'longlong':
            case 'smallint':
            case 'bigint':
            case 'tinyint':
            case 'byte':
            case 'counter':
            case 'year':
                return 'int';

            case 'bit':
                return 'boolean';

            case 'float':
            case 'float4':
            case 'float8':
            case 'double':
            case 'decimal':
            case 'newdecimal':
            case 'money':
            case 'numeric':
            case 'real':
            case 'smallmoney':
            case 'currency':
                return 'number';

            case 'string':
            case 'var_string':
            case 'char':
            case 'nchar':
            case 'ntext':
            case 'varchar':
            case 'nvarchar':
            case 'text':
            case 'uniqueidentifier':
            case 'xml':
                return 'string';

            case 'date':
            case 'datetime':
            case 'datetime2':
            case 'datetimeoffset':
            case 'smalldatetime':
                return 'datetime';

            case 'time':
            case 'timestamp':
                return 'time';

            case 'blob':
            case 'geometry':
            case 'binary':
            case 'image':
            case 'sql_variant':
            case 'varbinary':
            case 'longbinary':
            case 'cursor':
            case 'bytea':
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

            case 'time':
                $timestamp = strtotime($value);
                $format = date("H:i:s.v", $timestamp);
                if (strpos($format, '.v') > 0) $format = date("H:i:s.000", $timestamp);
                return $format;
        }

        return $value;
    }

    public function executeQuery($queryString, $maxDataRows): StiDataResult
    {
        $result = $this->connect();
        if ($result->success) {
            $query = odbc_exec($this->connectionLink, $queryString);
            if (!$query)
                return $this->getLastErrorResult();

            $result->types = [];
            $result->columns = [];
            $result->rows = [];

            $result->count = odbc_num_fields($query);

            for ($i = 1; $i <= $result->count; $i++) {
                $type = odbc_field_type($query, $i);
                $result->types[] = $this->getType($type);
                $result->columns[] = odbc_field_name($query, $i);
            }

            while (odbc_fetch_row($query)) {
                $row = [];

                for ($i = 1; $i <= $result->count; $i++) {
                    $type = $result->types[$i - 1];
                    $value = odbc_result($query, $i);
                    $row[] = $this->getValue($type, $value);
                }

                $result->rows[] = $row;

                if (count($result->rows) === $maxDataRows)
                    break;
            }

            odbc_free_result($query);
            $this->disconnect();
        }

        return $result;
    }
}