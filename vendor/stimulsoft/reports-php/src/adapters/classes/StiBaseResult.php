<?php

namespace Stimulsoft;

use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;

/**
 * The result of executing an event handler request. The result contains a collection of data,
 * message about the result of the command execution, and other technical information.
 */
class StiBaseResult implements JsonSerializable
{

### Properties

    public $handlerVersion = null;
    public $checkVersion = true;
    public $success = true;
    public $notice = null;


### Abstract

    public $types;


### JSON

    public function jsonSerialize(): array
    {
        $properties = StiFunctions::getProperties($this);
        $result = [];
        foreach ($properties as $name)
            $result[$name] = $this->$name;

        return $result;
    }


### Result

    /**
     * Creates a successful result.
     * @param string $notice Optionally, a message about the result.
     * @return StiBaseResult
     */
    public static function getSuccess(string $notice = null)
    {
        $result = new StiBaseResult();
        $result->success = true;
        $result->notice = $notice;
        return $result;
    }

    /**
     * Creates an error result.
     * @param string $notice The error message.
     * @return StiBaseResult
     */
    public static function getError(string $notice)
    {
        $result = new StiBaseResult();
        $result->success = false;
        $result->notice = $notice;
        return $result;
    }
}