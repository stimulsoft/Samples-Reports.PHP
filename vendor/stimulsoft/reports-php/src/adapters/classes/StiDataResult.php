<?php

namespace Stimulsoft;

use Stimulsoft\Adapters\StiDataAdapter;
use Stimulsoft\Adapters\StiSqlAdapter;

/**
 * The result of processing a request from the client side. The result contains a collection of data,
 * message about the result of the command execution, and other technical information.
 */
class StiDataResult extends StiBaseResult
{

### Properties

    public $adapterVersion = null;
    public $types;
    public $columns;
    public $rows;
    public $data;
    public $dataType;
    public $count;


### Helpers

    public function getType(): string
    {
        if ($this->success) {
            if (is_array($this->columns))
                return "SQL";

            if ($this->dataType != null)
                return "File";
        }

        return parent::getType();
    }


### Result

    public function getDataAdapterResult(StiDataAdapter $adapter): StiDataResult
    {
        $this->adapterVersion = $adapter->version;
        $this->checkVersion = $adapter->checkVersion;

        if ($adapter instanceof StiSqlAdapter) {
            $this->types = [];
            $this->columns = [];
            $this->rows = [];
        }

        return $this;
    }

    /**
     * Creates a successful result.
     * @param string|null $notice Optionally, a message about the result.
     * @return StiDataResult
     */
    public static function getSuccess(?string $notice = null)
    {
        $result = new StiDataResult();
        $result->success = true;
        $result->notice = $notice;

        return $result;
    }

    /**
     * Creates an error result.
     * @param string $notice The error message.
     * @return StiDataResult
     */
    public static function getError(string $notice)
    {
        $result = new StiDataResult();
        $result->success = false;
        $result->notice = $notice;

        return $result;
    }
}