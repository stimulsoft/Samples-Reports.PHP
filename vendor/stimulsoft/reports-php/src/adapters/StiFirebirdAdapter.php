<?php

namespace Stimulsoft\Adapters;

use Stimulsoft\Enums\StiDatabaseType;
use Stimulsoft\Events\StiConnectionEventArgs;
use Stimulsoft\StiDataResult;

class StiFirebirdAdapter extends StiDataAdapter
{

### Constants

    const DriverNotFound = 'Firebird driver not found. Please configure your PHP server to work with Firebird.';


### Properties

    public $version = '2025.1.2';
    public $checkVersion = true;

    protected $type = StiDatabaseType::Firebird;
    protected $driverName = 'firebird';


### Methods

    protected function getLastErrorResult(): StiDataResult
    {
        if ($this->driverType == 'PDO')
            return parent::getLastErrorResult();

        $code = ibase_errcode();
        $error = ibase_errmsg();
        $message = $error ?: self::UnknownError;
        if ($code != 0) $message = "[$code] $message";

        return StiDataResult::getError($message)->getDataAdapterResult($this);
    }

    protected function connect(): StiDataResult
    {
        if ($this->driverType == 'PDO')
            return parent::connect();

        if (!function_exists('ibase_connect'))
            return StiDataResult::getError(self::DriverNotFound)->getDataAdapterResult($this);

        $args = new StiConnectionEventArgs($this->type, $this->driverName, $this->connectionInfo);
        $this->handler->onDatabaseConnect->call($args);

        $this->connectionLink = $args->link !== null ? $args->link : ibase_connect(
            $this->connectionInfo->host . '/' . $this->connectionInfo->port . ':' . $this->connectionInfo->database,
            $this->connectionInfo->userId, $this->connectionInfo->password, $this->connectionInfo->charset);

        if (!$this->connectionLink)
            return $this->getLastErrorResult();

        return StiDataResult::getSuccess()->getDataAdapterResult($this);
    }

    protected function disconnect()
    {
        if ($this->driverType == 'PDO')
            parent::disconnect();
        else if ($this->connectionLink) {
            ibase_close($this->connectionLink);
            $this->connectionLink = null;
        }
    }

    public function process(): bool
    {
        if (parent::process()) return true;

        $this->connectionInfo->port = 3050;
        $this->connectionInfo->charset = 'UTF8';

        $parameterNames = array(
            'host' => ['server', 'host', 'location', 'datasource', 'data source'],
            'port' => ['port'],
            'database' => ['database', 'dbname'],
            'userId' => ['uid', 'user', 'username', 'userid', 'user id'],
            'password' => ['pwd', 'password'],
            'charset' => ['charset']
        );

        return $this->processParameters($parameterNames);
    }

    protected function getType($meta): string
    {
        switch ($meta) {
            case 'SMALLINT':
            case 'INTEGER':
            case 'BIGINT':
                return 'int';

            case 'FLOAT':
            case 'DOUBLE PRECISION':
            case 'NUMERIC':
            case 'DECIMAL':
                return 'number';

            case 'DATE':
            case 'TIMESTAMP':
                return 'datetime';

            case 'TIME':
                return 'time';

            case 'CHAR':
            case 'VARCHAR':
                return 'string';

            case 'BLOB':
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

            case 'string':
                return mb_convert_encoding($value, 'UTF-8', mb_list_encodings());
        }

        return $value;
    }

    public function makeQuery($procedure, $parameters): string
    {
        $paramsString = parent::makeQuery($procedure, $parameters);
        return "EXECUTE PROCEDURE $procedure $paramsString";
    }

    protected function executeNative($queryString, $maxDataRows, $result): StiDataResult
    {
        $query = ibase_query($this->connectionLink, $queryString);
        if (!$query)
            return $this->getLastErrorResult();

        $result->count = ibase_num_fields($query);

        for ($i = 0; $i < $result->count; $i++) {
            $meta = ibase_field_info($query, $i);
            $result->columns[] = $meta['name'];
            $result->types[] = $this->getType($meta['type']);
        }

        while ($rowItem = ibase_fetch_assoc($query, IBASE_TEXT)) {
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

    protected function executePDO($queryString, $maxDataRows, $result): StiDataResult
    {
        // PDO Firebird driver doesn't support getColumnMeta()
        // The type is determined by the first value

        return $this->executePDOv2($queryString, $maxDataRows, $result);
    }
}