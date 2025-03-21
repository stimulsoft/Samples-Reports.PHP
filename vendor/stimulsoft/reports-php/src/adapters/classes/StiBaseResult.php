<?php

namespace Stimulsoft;

use JsonSerializable;
use ReflectionClass;

/**
 * The result of processing a request from the client side. The result contains a collection of data,
 * message about the result of the command execution, and other technical information.
 */
class StiBaseResult implements JsonSerializable
{

### Properties

    public $handlerVersion = null;
    public $checkVersion = true;
    public $success = true;
    public $notice = null;


### JSON

    public function jsonSerialize(): array
    {
        $properties = StiFunctions::getProperties($this);
        $result = [];
        foreach ($properties as $name)
            $result[$name] = $this->$name;

        return $result;
    }


### Helpers

    public function getType(): string
    {
        return $this->success ? "Success" : "Error";
    }


### Result

    /**
     * Creates a successful result.
     * @param string|null $notice Optionally, a message about the result.
     * @return StiBaseResult
     */
    public static function getSuccess(?string $notice = null)
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