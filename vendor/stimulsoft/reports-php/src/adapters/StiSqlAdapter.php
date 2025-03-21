<?php

namespace Stimulsoft\Adapters;

use Stimulsoft\Events\StiConnectionEventArgs;
use Stimulsoft\StiConnectionInfo;
use Stimulsoft\StiDataResult;
use Stimulsoft\StiFunctions;

class StiSqlAdapter extends StiDataAdapter
{

### Properties

    /** @var StiConnectionInfo Parameters required to connect to the database. Taken automatically from the connection string. */
    protected $connectionInfo;


### Methods

    protected function getLastErrorResult(): StiDataResult
    {
        $info = $this->connectionLink->errorInfo();
        $code = $info[0];
        $message = count($info) >= 3 ? $info[2] : StiDataAdapter::UnknownError;
        if ($code != 0) $message = "[$code] $message";

        return StiDataResult::getError($message, $this);
    }

    protected function connect(): StiDataResult
    {
        try {
            $args = new StiConnectionEventArgs($this->handler->request, $this->type, "pdo_$this->driverName", $this->connectionInfo);
            $this->handler->onDatabaseConnect->call($args);

            $this->connectionLink = $args->link !== null
                ? $args->link
                : new PDO($this->connectionInfo->dsn, $this->connectionInfo->userId, $this->connectionInfo->password);
        }
        catch (PDOException $e) {
            $code = $e->getCode();
            $message = $e->getMessage();
            if ($code != 0) $message = "[$code] $message";

            return StiDataResult::getError($message, $this);
        }

        return parent::connect();
    }

    public function process(): bool
    {
        $this->connectionInfo = new StiConnectionInfo();

        if (!StiFunctions::isNullOrEmpty($this->connectionString) && mb_strpos($this->connectionString, "$this->driverName:") !== false) {
            $this->driverType = 'PDO';

            $parameterNames = array(
                'userId' => ['uid', 'user', 'username', 'userid', 'user id'],
                'password' => ['pwd', 'password']
            );

            return $this->processParameters($parameterNames);
        }

        return parent::process();
    }

    protected function processParameters($parameterNames): bool
    {
        $parameters = explode(';', $this->connectionString);

        foreach ($parameters as $parameter) {
            $name = '';
            $value = $parameter;
            if (mb_strpos($parameter, '=') >= 0) {
                $pos = mb_strpos($parameter, '=');
                $name = mb_strtolower(trim(mb_substr($parameter, 0, $pos)));
                $value = trim(mb_substr($parameter, $pos + 1));
            }

            $unknown = true;
            foreach ($parameterNames as $key => $names) {
                if (in_array($name, $names)) {
                    $this->connectionInfo->{$key} = $value;
                    $unknown = false;
                    break;
                }
            }

            if ($unknown)
                $this->processUnknownParameter($parameter, $name, $value);
        }

        return true;
    }

    protected function processUnknownParameter($parameter, $name, $value)
    {
        if ($this->driverType == 'PDO' && !is_null($parameter) && mb_strlen($parameter) > 0) {
            if (mb_strlen($this->connectionInfo->dsn) > 0)
                $this->connectionInfo->dsn .= ';';

            $this->connectionInfo->dsn .= $parameter;
        }
    }

    protected function getType($meta): string
    {
        return 'string';
    }

    protected function getValueType($value): string
    {
        if (empty($value))
            return 'string';

        if (preg_match('~[^\x20-\x7E\t\r\n]~', $value) > 0)
            return 'array';

        if (is_numeric($value)) {
            if (strpos($value, '.') !== false) return 'number';
            return 'int';
        }

        if (DateTime::createFromFormat('Y-m-d H:i:s', $value) !== false ||
            DateTime::createFromFormat('Y-m-d', $value) !== false ||
            DateTime::createFromFormat('Y-M-d', $value) !== false ||
            DateTime::createFromFormat('H:i:s', $value) !== false)
            return 'datetime';

        if (is_string($value))
            return 'string';

        return 'array';
    }

    protected function getValue($type, $value)
    {
        return $value;
    }

    public function makeQuery($procedure, $parameters): string
    {
        $paramsString = '';
        foreach ($parameters as $name => $parameter) {
            if (strlen($paramsString) > 0)
                $paramsString .= ', ';

            $paramsString .= "@$name";
        }

        return $paramsString;
    }

    public function executeQuery($queryString, $maxDataRows): StiDataResult
    {
        $result = $this->connect();
        if ($result->success) {
            $result->types = [];
            $result->columns = [];
            $result->rows = [];

            if ($maxDataRows !== 0)
                $result = $this->driverType == 'PDO'
                    ? $this->executePDO($queryString, $maxDataRows, $result)
                    : $this->executeNative($queryString, $maxDataRows, $result);

            $this->disconnect();
        }

        return $result;
    }

    protected function executePDO($queryString, $maxDataRows, $result): StiDataResult
    {
        $query = $this->connectionLink->query($queryString);
        if (!$query)
            return $this->getLastErrorResult();

        $result->count = $query->columnCount();

        for ($i = 0; $i < $result->count; $i++) {
            $meta = $query->getColumnMeta($i);
            $result->columns[] = $meta['name'];
            $result->types[] = $this->getType($meta);
        }

        while ($rowItem = $query->fetch()) {
            $row = [];

            for ($i = 0; $i < $result->count; $i++) {
                $type = count($result->types) >= $i + 1 ? $result->types[$i] : 'string';
                $row[] = $this->getValue($type, $rowItem[$i]);
            }

            $result->rows[] = $row;

            if (count($result->rows) === $maxDataRows)
                break;
        }

        return $result;
    }

    protected function executePDOv2($queryString, $maxDataRows, $result): StiDataResult
    {
        $query = $this->connectionLink->query($queryString);
        if (!$query)
            return $this->getLastErrorResult();

        $result->count = $query->columnCount();

        while ($rowItem = $query->fetch()) {
            $index = 0;
            $row = [];

            foreach ($rowItem as $key => $value) {
                if (is_string($key)) {
                    $index++;
                    if (count($result->columns) < $index) $result->columns[] = $key;
                    if (count($result->types) < $index) $result->types[] = $this->getValueType($value);
                    $type = $result->types[$index - 1];
                    $row[] = $this->getValue($type, $value);
                }
            }

            $result->rows[] = $row;

            if (count($result->rows) === $maxDataRows)
                break;
        }

        return $result;
    }

    protected function executeNative($queryString, $maxDataRows, $result): StiDataResult
    {
        return $result;
    }

    public function getDataResult($queryString, $maxDataRows = -1): StiDataResult
    {
        return $this->executeQuery($queryString, $maxDataRows);
    }


### Helpers

    public static function applyQueryParameters($query, $parameters, $escape): string
    {
        $result = '';

        while (mb_strpos($query, '@') !== false) {
            $result .= mb_substr($query, 0, mb_strpos($query, '@'));
            $query = mb_substr($query, mb_strpos($query, '@') + 1);

            $parameterName = '';
            while (strlen($query) > 0) {
                $char = mb_substr($query, 0, 1);
                if (!preg_match('/[a-zA-Z0-9_-]/', $char)) break;

                $parameterName .= $char;
                $query = mb_substr($query, 1);
            }

            $replaced = false;
            foreach ($parameters as $key => $item) {
                if (strtolower($key) == strtolower($parameterName)) {
                    switch ($item->typeGroup) {
                        case 'number':
                            $result .= floatval($item->value);
                            break;

                        case 'datetime':
                            $result .= "'" . $item->value . "'";
                            break;

                        default:
                            $result .= "'" . ($escape ? addcslashes($item->value, "\\\"'") : $item->value) . "'";
                            break;
                    }

                    $replaced = true;
                }
            }

            if (!$replaced) $result .= '@' . $parameterName;
        }

        return $result . $query;
    }
}