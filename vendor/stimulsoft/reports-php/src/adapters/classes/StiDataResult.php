<?php

namespace Stimulsoft;

use Stimulsoft\Adapters\StiDataAdapter;

/**
 * The result of executing an event handler request. The result contains a collection of data,
 * message about the result of the command execution, and other technical information.
 */
class StiDataResult extends StiBaseResult
{

### Properties

    public $adapterVersion;
    public $types;
    public $columns;
    public $rows;
    public $count = 0;


### Result

    public function getDataAdapterResult(StiDataAdapter $adapter): StiDataResult
    {
        $this->adapterVersion = $adapter->version;
        $this->checkVersion = $adapter->checkVersion;
        return $this;
    }

    /**
     * Creates a successful result.
     * @param string $notice Optionally, a message about the result.
     */
    public static function getSuccess(string $notice = null)
    {
        $result = new StiDataResult();
        $result->success = true;
        $result->notice = $notice;
        $result->types = [];
        $result->columns = [];
        $result->rows = [];

        return $result;
    }

    /**
     * Creates an error result.
     * @param string $notice The error message.
     */
    public static function getError(string $notice)
    {
        $result = new StiDataResult();
        $result->success = false;
        $result->notice = $notice;

        return $result;
    }
}