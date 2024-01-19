<?php

namespace Stimulsoft\Adapters;

use Stimulsoft\StiConnectionInfo;
use Stimulsoft\StiDataResult;
use Stimulsoft\StiResult;

class StiMongoDbAdapter extends StiDataAdapter
{
    public $version = '2024.1.3';
    public $checkVersion = true;

    protected $driverName = 'mongodb';

    protected function getLastErrorResult($message = 'An unknown error has occurred.')
    {
        if ($this->driverType == 'PDO')
            return parent::getLastErrorResult($message);

        return StiResult::error($message);
    }

    protected function connect()
    {
        if ($this->driverType == 'PDO')
            return parent::connect();

        if (!class_exists('\MongoDB\Driver\Manager'))
            return StiResult::error('MongoDB driver not found. Please configure your PHP server to work with MongoDB.');

        if ($this->connectionInfo->database == '')
            return StiResult::error('The database name cannot be empty.');

        try {
            $this->connectionLink = new \MongoDB\Driver\Manager($this->connectionString);
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->getLastErrorResult($message);
        }

        return StiDataResult::success();
    }

    protected function disconnect()
    {
        if ($this->driverType == 'PDO')
            parent::disconnect();
        else if ($this->connectionLink)
            $this->connectionLink = null;
    }

    public function parse($connectionString)
    {
        $this->connectionInfo = new StiConnectionInfo();
        $this->connectionString = trim($connectionString);

        $url = parse_url($connectionString);
        $this->connectionInfo->host = isset($url['host']) ? $url['host'] : '';
        $this->connectionInfo->port = isset($url['port']) ? $url['port'] : 27017;
        $this->connectionInfo->userId = isset($url['user']) ? $url['user'] : '';
        $this->connectionInfo->password = isset($url['pass']) ? $url['pass'] : '';
        $this->connectionInfo->database = isset($url['path']) ? trim($url['path'], '/') : '';

        return true;
    }

    protected function parseParameters($parameterNames)
    {
        return true;
    }

    protected function parseType($meta)
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

    protected function executeNative($queryString, $result)
    {
        if (empty($queryString))
            return $this->retrieveSchema($result);

        return $this->retrieveData($result, $queryString);
    }

    private function retrieveSchema($result)
    {
        $command = new \MongoDB\Driver\Command(['listCollections' => 1]);
        $cursor = $this->connectionLink->executeReadCommand($this->connectionInfo->database, $command);

        $schema = array();
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
            $command = new \MongoDB\Driver\Command(['aggregate' => $collection->name, 'pipeline' => $pipeline, 'cursor' => new \stdClass()]);
            $cursor = $this->connectionLink->executeReadCommand($this->connectionInfo->database, $command);
            $fields = $cursor->toArray();
            if (count($fields) > 0)
                $schema[$collection->name] = $fields[0];
        }

        $result->count = count($schema);
        foreach ($schema as $tableName => $table) {
            foreach ($table as $columnName => $columnType) {
                $row = array();
                $row[] = $tableName;
                $row[] = $columnName;
                $row[] = $this->parseType($columnType);
                $result->rows[] = $row;
            }
        }

        return $result;
    }

    private function retrieveData($result, $queryString)
    {
        $result = $this->retrieveSchema($result);
        foreach ($result->rows as $item) {
            if ($item[0] == $queryString) {
                $result->columns[] = $item[1];
                $result->types[] = $item[2];
            }
        }
        $result->count = count($result->columns);
        $result->rows = array();

        $filter = [];
        $options = [];
        $query = new \MongoDB\Driver\Query($filter, $options);
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