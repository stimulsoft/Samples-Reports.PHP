<?php

namespace Stimulsoft\Adapters;

use Exception;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use stdClass;
use Stimulsoft\Enums\StiDatabaseType;
use Stimulsoft\Events\StiConnectionEventArgs;
use Stimulsoft\StiConnectionInfo;
use Stimulsoft\StiDataResult;

class StiMongoDbAdapter extends StiDataAdapter
{

### Constants

    const DriverNotFound = 'MongoDB driver not found. Please configure your PHP server to work with MongoDB.';


### Properties

    public $version = '2025.1.2';
    public $checkVersion = true;

    protected $type = StiDatabaseType::MongoDB;
    protected $driverName = 'mongodb';


### Methods

    protected function getLastErrorResult(): StiDataResult
    {
        if ($this->driverType == 'PDO')
            return parent::getLastErrorResult();

        return StiDataResult::getError(self::UnknownError)->getDataAdapterResult($this);
    }

    protected function connect(): StiDataResult
    {
        if ($this->driverType == 'PDO')
            return parent::connect();

        if (!class_exists('\MongoDB\Driver\Manager'))
            return StiDataResult::getError(self::DriverNotFound)->getDataAdapterResult($this);

        if ($this->connectionInfo->database == '')
            return StiDataResult::getError('The database name cannot be empty.')->getDataAdapterResult($this);

        try {
            $args = new StiConnectionEventArgs($this->type, $this->driverName, $this->connectionInfo);
            $this->handler->onDatabaseConnect->call($args);

            $this->connectionLink = $args->link !== null ? $args->link : new Manager($this->connectionString);
        }
        catch (Exception $e) {
            $message = $e->getMessage();
            return StiDataResult::getError($message)->getDataAdapterResult($this);
        }

        return StiDataResult::getSuccess()->getDataAdapterResult($this);
    }

    protected function disconnect()
    {
        if ($this->driverType == 'PDO')
            parent::disconnect();
        else if ($this->connectionLink)
            $this->connectionLink = null;
    }

    public function process(): bool
    {
        $this->connectionInfo = new StiConnectionInfo();

        $url = parse_url($this->connectionString);
        $this->connectionInfo->host = $url['host'] ?? '';
        $this->connectionInfo->port = $url['port'] ?? 27017;
        $this->connectionInfo->userId = $url['user'] ?? '';
        $this->connectionInfo->password = $url['pass'] ?? '';
        $this->connectionInfo->database = isset($url['path']) ? trim($url['path'], '/') : '';

        $parameterNames = [];
        return $this->processParameters($parameterNames);
    }

    protected function processParameters($parameterNames): bool
    {
        return true;
    }

    protected function getType($meta): string
    {
        switch ($meta) {
            case 'bool':
                return 'boolean';

            case 'int':
            case 'long':
            case 'minKey':
            case 'maxKey':
                return 'int';

            case 'double':
            case 'decimal':
                return 'number';

            case 'date':
            case 'timestamp':
                return 'datetime';

            case 'string':
            case 'objectId':
            case 'regex':
            case 'javascript':
            case 'array':
            case 'object':
                return 'string';

            case 'binData':
            case 'null':
                return 'array';
        }

        return 'string';
    }

    protected function getValue($type, $value)
    {
        if (is_null($value))
            return null;

        switch ($type) {
            case 'datetime':
                $dateTime = $value->toDateTime();
                $format = $dateTime->format("Y-m-d\TH:i:s.v");
                if (strpos($format, '.v') > 0) $format = $dateTime->format("Y-m-d\TH:i:s.000");
                return $format;

            case 'number':
                $number = (string)$value;
                return floatval($number);

            case 'string':
                if (is_array($value) || is_object($value)) $value = json_encode($value);
                return $value;

            case 'array':
                return base64_encode(json_encode($value));

            case 'time':
                $timestamp = strtotime($value);
                $format = date("H:i:s.v", $timestamp);
                if (strpos($format, '.v') > 0) $format = date("H:i:s.000", $timestamp);
                return $format;
        }

        return $value;
    }

    protected function executeNative($queryString, $maxDataRows, $result): StiDataResult
    {
        if (empty($queryString))
            return $this->retrieveSchema($result);

        return $this->retrieveData($result, $queryString, $maxDataRows);
    }

    private function retrieveSchema($result)
    {
        $command = new Command(['listCollections' => 1]);
        $cursor = $this->connectionLink->executeReadCommand($this->connectionInfo->database, $command);

        $schema = [];
        foreach ($cursor as $collection) {
            $pipeline = [
                ['$project' => ['_id' => 0]],
                ['$project' => ['data' => ['$objectToArray' => '$$ROOT']]],
                ['$unwind' => '$data'],
                ['$group' => [
                    '_id' => null,
                    'data' => ['$addToSet' => ['k' => '$data.k', 'v' => ['$type' => '$data.v']]]]
                ],
                ['$replaceRoot' => ['newRoot' => ['$arrayToObject' => '$data']]]
            ];
            $command = new Command(['aggregate' => $collection->name, 'pipeline' => $pipeline, 'cursor' => new stdClass()]);
            $cursor = $this->connectionLink->executeReadCommand($this->connectionInfo->database, $command);
            $fields = $cursor->toArray();
            if (count($fields) > 0)
                $schema[$collection->name] = $fields[0];
        }

        $result->count = count($schema);
        foreach ($schema as $tableName => $table) {
            foreach ($table as $columnName => $columnType) {
                $row = [];
                $row[] = $tableName;
                $row[] = $columnName;
                $row[] = $this->getType($columnType);
                $result->rows[] = $row;
            }
        }

        return $result;
    }

    private function retrieveData($result, $queryString, $maxDataRows)
    {
        $result = $this->retrieveSchema($result);
        foreach ($result->rows as $item) {
            if ($item[0] == $queryString) {
                $result->columns[] = $item[1];
                $result->types[] = $item[2];
            }
        }
        $result->count = count($result->columns);
        $result->rows = [];

        $filter = [];
        $options = [];
        if ($maxDataRows !== null)
            $options['limit'] = $maxDataRows;

        $query = new Query($filter, $options);
        $cursor = $this->connectionLink->executeQuery($this->connectionInfo->database . '.' . $queryString, $query);
        foreach ($cursor as $document) {
            $row = array_fill(0, $result->count, null);
            foreach ($document as $key => $value) {
                $index = array_search($key, $result->columns);
                if ($index !== false) $row[$index] = $this->getValue($result->types[$index], $value);
            }

            $result->rows[] = $row;
        }

        $result->count = count($result->rows);
        return $result;
    }
}