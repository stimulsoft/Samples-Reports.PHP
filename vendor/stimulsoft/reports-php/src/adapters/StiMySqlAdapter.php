<?php

namespace Stimulsoft\Adapters;

use mysqli;
use Stimulsoft\Enums\StiDatabaseType;
use Stimulsoft\Events\StiConnectionEventArgs;
use Stimulsoft\StiDataResult;

class StiMySqlAdapter extends StiDataAdapter
{

### Properties

    public $version = '2025.1.2';
    public $checkVersion = true;

    protected $type = StiDatabaseType::MySQL;
    protected $driverName = 'mysql';


### Methods

    protected function getLastErrorResult(): StiDataResult
    {
        if ($this->driverType == 'PDO')
            return parent::getLastErrorResult();

        $message = $this->connectionLink->error ?: self::UnknownError;
        if ($this->connectionLink->errno != 0) $message = "[{$this->connectionLink->errno}] $message";

        return StiDataResult::getError($message)->getDataAdapterResult($this);
    }

    protected function connect(): StiDataResult
    {
        if ($this->driverType == 'PDO')
            return parent::connect();

        $args = new StiConnectionEventArgs($this->type, $this->driverName, $this->connectionInfo);
        $this->handler->onDatabaseConnect->call($args);

        $this->connectionLink = $args->link !== null ? $args->link : new mysqli(
            $this->connectionInfo->host, $this->connectionInfo->userId, $this->connectionInfo->password,
            $this->connectionInfo->database, $this->connectionInfo->port);

        if ($this->connectionLink->connect_error)
            return StiDataResult::getError("[{$this->connectionLink->connect_errno}] {$this->connectionLink->connect_error}")->getDataAdapterResult($this);

        if (!$this->connectionLink->set_charset($this->connectionInfo->charset))
            return $this->getLastErrorResult();

        return StiDataResult::getSuccess()->getDataAdapterResult($this);
    }

    protected function disconnect()
    {
        if ($this->driverType == 'PDO')
            parent::disconnect();
        else if ($this->connectionLink) {
            $this->connectionLink->close();
            $this->connectionLink = null;
        }
    }

    public function process(): bool
    {
        if (parent::process())
            return true;

        $this->connectionInfo->port = 3306;
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

    private function getStringType($meta): string
    {
        switch ($meta->type) {
            case MYSQLI_TYPE_TINY:
                return 'tiny';

            case MYSQLI_TYPE_SHORT:
            case MYSQLI_TYPE_LONG:
            case MYSQLI_TYPE_LONGLONG:
            case MYSQLI_TYPE_INT24:
            case MYSQLI_TYPE_YEAR:
                return 'int';

            case MYSQLI_TYPE_BIT:
                return 'bit';

            case MYSQLI_TYPE_DECIMAL:
            case MYSQLI_TYPE_FLOAT:
            case MYSQLI_TYPE_DOUBLE:
            case MYSQLI_TYPE_NEWDECIMAL:
                return 'decimal';

            case MYSQLI_TYPE_TIMESTAMP:
            case MYSQLI_TYPE_DATE:
            case MYSQLI_TYPE_DATETIME:
            case MYSQLI_TYPE_NEWDATE:
                return 'datetime';

            case MYSQLI_TYPE_TIME:
                return 'time';

            case MYSQLI_TYPE_STRING:
            case MYSQLI_TYPE_VAR_STRING:
            case MYSQLI_TYPE_BLOB:
                return $this->isBinaryStringType($meta) ? 'blob' : 'string';

            case MYSQLI_TYPE_TINY_BLOB:
            case MYSQLI_TYPE_MEDIUM_BLOB:
            case MYSQLI_TYPE_LONG_BLOB:
            case MYSQLI_TYPE_GEOMETRY:
                return 'blob';
        }

        return 'string';
    }

    private function isBinaryStringType($meta): bool
    {
        // BINARY_ENCODING = 63, see https://github.com/sidorares/node-mysql2/blob/ef283413607a5ee6643c238245f3ad4b533f5689/lib/constants/charsets.js#L64
        return ($meta->flags & MYSQLI_BINARY_FLAG) && ($meta->charsetnr == 63);
    }

    protected function getType($meta): string
    {
        $binary = false;

        if ($this->driverType == 'PDO') {
            foreach ($meta['flags'] as $value) {
                if ($value == 'blob')
                    $binary = true;
            }
            $type = $meta['native_type'];
            $length = $meta['len'];
        }
        else {
            $type = $this->getStringType($meta);
            $length = $meta->length;
        }

        $type = strtolower($type);
        switch ($type) {
            case 'short':
            case 'int':
            case 'int24':
            case 'long':
            case 'longlong':
            case 'bit':
                return 'int';

            case 'decimal':
            case 'newdecimal':
            case 'float':
            case 'double':
                return 'number';

            case 'tiny':
                return $length == 1 ? 'boolean' : 'int';

            case 'string':
            case 'var_string':
                return $binary ? 'array' : 'string';

            case 'date':
            case 'datetime':
            case 'timestamp':
            case 'year':
                return 'datetime';

            case 'time':
                return 'time';

            case 'blob':
            case 'geometry':
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
                // Replace invalid dates with NULL
                if ($value == "0000-00-00 00:00:00") return null;
                $timestamp = strtotime($value);
                $format = date("Y-m-d\TH:i:s.v", $timestamp);
                if (strpos($format, '.v') > 0) $format = date("Y-m-d\TH:i:s.000", $timestamp);
                return $format;

            case 'time':
                $hours = intval($value);
                if ($hours < 0 || $hours > 23) return $value;

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
        $query = $this->connectionLink->query($queryString);
        if (!$query)
            return $this->getLastErrorResult();

        $result->count = $query->field_count;

        while ($meta = $query->fetch_field()) {
            $result->columns[] = $meta->name;
            $result->types[] = $this->getType($meta);
        }

        if ($query->num_rows > 0) {
            $isColumnsEmpty = count($result->columns) == 0;
            while ($rowItem = $isColumnsEmpty ? $query->fetch_assoc() : $query->fetch_row()) {
                $row = [];

                foreach ($rowItem as $key => $value) {
                    if ($isColumnsEmpty && count($result->columns) < count($rowItem)) $result->columns[] = $key;
                    $type = count($result->types) >= count($row) + 1 ? $result->types[count($row)] : 'string';
                    $row[] = $this->getValue($type, $value);
                }

                $result->rows[] = $row;

                if (count($result->rows) === $maxDataRows)
                    break;
            }
        }

        return $result;
    }
}