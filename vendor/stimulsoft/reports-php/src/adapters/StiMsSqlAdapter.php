<?php

namespace Stimulsoft\Adapters;

use Stimulsoft\Enums\StiDatabaseType;
use Stimulsoft\Events\StiConnectionEventArgs;
use Stimulsoft\StiDataResult;

class StiMsSqlAdapter extends StiDataAdapter
{

### Constants

    const DriverNotFound = 'MS SQL driver not found. Please configure your PHP server to work with MS SQL.';


### Properties

    public $version = '2025.1.2';
    public $checkVersion = true;

    protected $type = StiDatabaseType::MSSQL;
    protected $driverName = 'sqlsrv';


### Methods

    protected function getLastErrorResult(): StiDataResult
    {
        if ($this->driverType == 'PDO')
            return parent::getLastErrorResult();

        $message = self::UnknownError;
        $errors = sqlsrv_errors();
        if ($errors != null) {
            $error = $errors[count($errors) - 1];
            $message = "[{$error['code']}] " . $error['message'];
        }

        return StiDataResult::getError($message)->getDataAdapterResult($this);
    }

    protected function connect(): StiDataResult
    {
        if ($this->driverType == 'PDO')
            return parent::connect();

        if (!function_exists('sqlsrv_connect'))
            return StiDataResult::getError(self::DriverNotFound)->getDataAdapterResult($this);

        $args = new StiConnectionEventArgs($this->type, $this->driverName, $this->connectionInfo);
        $this->handler->onDatabaseConnect->call($args);

        if ($args->link !== null)
            $this->connectionLink = $args->link;
        else {
            sqlsrv_configure('WarningsReturnAsErrors', 0);
            $this->connectionLink = sqlsrv_connect(
                $this->connectionInfo->host,
                array(
                    'UID' => $this->connectionInfo->userId,
                    'PWD' => $this->connectionInfo->password,
                    'Database' => $this->connectionInfo->database,
                    'LoginTimeout' => 10,
                    'ReturnDatesAsStrings' => true,
                    'CharacterSet' => $this->connectionInfo->charset
                ));
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
            sqlsrv_close($this->connectionLink);
            $this->connectionLink = null;
        }
    }

    public function process(): bool
    {
        if (parent::process()) return true;

        $this->driverType = 'Microsoft';
        $this->connectionInfo->charset = 'UTF-8';

        $parameterNames = array(
            'host' => ['server', 'data source'],
            'database' => ['database', 'initial catalog', 'dbname'],
            'userId' => ['uid', 'user', 'user id'],
            'password' => ['pwd', 'password'],
            'charset' => ['charset']
        );

        return $this->processParameters($parameterNames);
    }

    private function getStringType($type): string
    {
        switch ($type) {
            case -6:
            case -5:
            case 4:
            case 5:
                return 'int';

            case 2:
            case 3:
            case 6:
            case 7:
                return 'decimal';

            case -7:
                return 'bit';

            case 91:
            case 93:
                return 'datetime';

            case -155:
                return 'datetimeoffset';

            case -154:
                return 'time';

            case -152:
            case -11:
            case -10:
            case -9:
            case -8:
            case -2:
            case -1:
            case 1:
            case 12:
                return 'string';

            case -151:
                return 'geometry'; // 'udt'

            case -150:
            case -4:
            case -3:
                return 'binary';
        }

        return 'string';
    }

    protected function getType($meta): string
    {
        if ($this->driverType == 'PDO') {
            $type = $meta['sqlsrv:decl_type'];
            //$length = $meta['len'];
        }
        else {
            $type = $this->getStringType($meta['Type']);
            //$length = $meta['Size'];
        }

        switch ($type) {
            case 'bigint':
            case 'int':
            case 'smallint':
            case 'tinyint':
                return 'int';

            case 'decimal':
            case 'float':
            case 'money':
            case 'numeric':
            case 'real':
            case 'smallmoney':
                return 'number';

            case 'bit':
                return 'boolean';

            case 'char':
            case 'nchar':
            case 'ntext':
            case 'nvarchar':
            case 'text':
            case 'timestamp':
            case 'uniqueidentifier':
            case 'varchar':
            case 'xml':
                return 'string';

            case 'date':
            case 'datetime':
            case 'datetime2':
            case 'smalldatetime':
                return 'datetime';

            case 'datetimeoffset':
                return 'datetimeoffset';

            case 'time':
                return 'time';

            case 'binary':
            case 'image':
            case 'sql_variant':
            case 'varbinary':
            case 'cursor':
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
                $offset = substr($value, strpos($value, '+'));
                $value = substr($value, 0, strpos($value, '+'));
                $timestamp = strtotime($value);
                $format = date("Y-m-d\TH:i:s.v", $timestamp);
                if (strpos($format, '.v') > 0) $format = date("Y-m-d\TH:i:s.000", $timestamp);
                return $format . $offset;

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
        return "EXEC $procedure $paramsString";
    }

    protected function executeNative($queryString, $maxDataRows, $result): StiDataResult
    {
        $query = sqlsrv_query($this->connectionLink, $queryString);
        if (!$query)
            return $this->getLastErrorResult();

        if ($this->driverType == 'Microsoft') {
            foreach (sqlsrv_field_metadata($query) as $meta) {
                $result->columns[] = $meta['Name'];
                $result->types[] = $this->getType($meta);
            }
        }

        $isColumnsEmpty = count($result->columns) == 0;
        while ($rowItem = sqlsrv_fetch_array($query, $isColumnsEmpty ? SQLSRV_FETCH_ASSOC : SQLSRV_FETCH_NUMERIC)) {
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

        return $result;
    }
}