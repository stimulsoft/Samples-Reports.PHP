<?php

namespace Stimulsoft\Adapters;

use Stimulsoft\Enums\StiDatabaseType;
use Stimulsoft\Events\StiConnectionEventArgs;
use Stimulsoft\StiConnectionInfo;
use Stimulsoft\StiDataResult;

class StiOdbcAdapter extends StiDataAdapter
{

### Properties

    public $version = '2025.1.2';
    public $checkVersion = true;

    protected $type = StiDatabaseType::ODBC;
    protected $driverName = 'odbc';


### Methods

    protected function getLastErrorResult(): StiDataResult
    {
        if ($this->driverType == 'PDO')
            return parent::getLastErrorResult();

        $code = odbc_error();
        $error = odbc_errormsg();
        $message = $error ?: self::UnknownError;
        if ($code != 0) $message = "[$code] $message";

        return StiDataResult::getError($message)->getDataAdapterResult($this);
    }

    protected function connect(): StiDataResult
    {
        if ($this->driverType == 'PDO')
            return parent::connect();

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
        if ($this->driverType == 'PDO')
            parent::disconnect();
        else if ($this->connectionLink) {
            odbc_close($this->connectionLink);
            $this->connectionLink = null;
        }
    }

    public function process(): bool
    {
        if (parent::process())
            return true;

        $parameterNames = array(
            'userId' => ['uid', 'user', 'username', 'userid', 'user id'],
            'password' => ['pwd', 'password']
        );

        return $this->processParameters($parameterNames);
    }

    protected function processUnknownParameter($parameter, $name, $value)
    {
        if (!is_null($parameter) && mb_strlen($parameter) > 0) {
            if (mb_strlen($this->connectionInfo->dsn) > 0)
                $this->connectionInfo->dsn .= ';';

            $this->connectionInfo->dsn .= $parameter;
        }
    }

    protected function getType($meta): string
    {
        $type = $this->driverType == 'PDO' ? $meta['pdo_type'] : strtolower($meta);

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
            case \PDO::PARAM_INT:
                return 'int';

            case 'bit':
            case \PDO::PARAM_BOOL:
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
            case \PDO::PARAM_STR:
            case \PDO::PARAM_STR_NATL:
            case \PDO::PARAM_STR_CHAR:
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
            case \PDO::PARAM_LOB:
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

    public function executeNative($queryString, $maxDataRows, $result): StiDataResult
    {
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

        return $result;
    }
}