<?php

namespace Stimulsoft;

use Exception;
use ReflectionClass;
use ReflectionProperty;

/**
 * Contains all set request parameters passed to the event handler.
 */
class StiBaseRequest
{

### Properties

    public $event;
    public $command;
    public $encryptData = false;
    public $connectionString;
    public $queryString;
    public $parameters;
    public $database;
    public $dataSource;
    public $connection;
    public $timeout = 0;
    public $maxDataRows;
    public $escapeQueryParameters = false;
    public $error = null;


### Helpers

    protected function setProperty($name, $value)
    {
        $this->$name = $value;
    }

    private function setObject($object, $prefix = false)
    {
        $properties = StiFunctions::getProperties($this);
        foreach ($object as $property => $value) {
            $name = $prefix && substr($property, 0, 4) == 'sti_' ? substr($property, 4) : $property;
            if (in_array($name, $properties))
                $this->setProperty($name, $value);
        }
    }


### Process

    public function process($query, $body): bool
    {
        if (count($query) > 0)
            $this->setObject($_GET, true);

        if ($body !== null && strlen($body) > 0) {
            if (mb_substr($body, 0, 1) != '{') {
                try {
                    $body = str_rot13($body);
                    $body = base64_decode($body);
                }
                catch (Exception $e) {
                    $this->error = 'Base64: ' . $e->getMessage();
                    return false;
                }

                $this->encryptData = true;
            }

            try {
                $obj = json_decode($body);
            }
            catch (Exception $e) {
                $this->error = 'JSON: ' . $e->getMessage();
                return false;
            }

            $this->setObject($obj);
        }

        return true;
    }

    /**
     * The result of executing an event handler request. The result contains a collection of data,
     * message about the result of the command execution, and other technical information.
     */
    public function getResult(): StiBaseResult
    {
        if ($this->error !== null)
            return StiBaseResult::getError($this->error);

        return StiBaseResult::getSuccess();
    }
}