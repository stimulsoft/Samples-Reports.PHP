<?php

namespace Stimulsoft;

/**
 * The result of processing a request from the client side. The result object will contain a collection of data,
 * message about the result of the command execution, and other technical information.
 */
class StiResult extends StiBaseResult
{

### Abstract

    public $fileName;
    public $variables;
    public $settings;
    public $report;
    public $pageRange;


### JSON

    public function jsonSerialize(): array
    {
        $properties = StiFunctions::getProperties($this);
        $result = [];
        foreach ($properties as $name) {
            $value = $this->$name;
            $result[$name] = $value instanceof StiJsElement ? $value->getObject() : $value;
        }

        return $result;
    }


### Result

    /**
     * Creates a successful result.
     * @param string|null $notice Optionally, a message about the result.
     */
    public static function getSuccess(string $notice = null)
    {
        $result = new StiResult();
        $result->success = true;
        $result->notice = $notice;

        return $result;
    }

    /**
     * Creates an error result.
     * @param string $notice The error message.
     */
    public static function getError(string $notice)
    {
        $result = new StiResult();
        $result->success = false;
        $result->notice = $notice;

        return $result;
    }

    /**
     * Creates a successful result.
     * @param string|null $notice Optionally, a message about the result.
     * @deprecated Please use the 'getSuccess()' method.
     */
    public static function success(string $notice = null)
    {
        $result = new StiResult();
        $result->success = true;
        $result->notice = $notice;

        return $result;
    }

    /**
     * Creates an error result.
     * @param string $notice The error message.
     * @deprecated Please use the 'getError()' method.
     */
    public static function error(string $notice)
    {
        $result = new StiResult();
        $result->success = false;
        $result->notice = $notice;

        return $result;
    }
}