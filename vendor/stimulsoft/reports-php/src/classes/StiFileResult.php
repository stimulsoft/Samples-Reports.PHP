<?php

namespace Stimulsoft;

use Stimulsoft\Enums\StiDataType;

/**
 * The result of processing a request from the client side. The result object will contain a collection of data,
 * message about the result of the command execution, and other technical information.
 */
class StiFileResult extends StiBaseResult
{

### Properties

    public $data;
    public $dataType;


### Helpers

    public function getType(): string
    {
        if ($this->success && $this->dataType != null)
            return "File";

        return parent::getType();
    }


### Result

    /**
     * Creates an error result.
     * @param string $notice The error message.
     */
    public static function getError(string $notice)
    {
        $result = new StiFileResult();
        $result->success = false;
        $result->notice = $notice;

        return $result;
    }


### Constructor

    /**
     * @param string|null $data
     * @param StiDataType|string|null $dataType [enum]
     */
    public function __construct(?string $data = null, ?string $dataType = null)
    {
        if ($data !== null)
            $this->data = $data;

        if ($dataType !== null)
            $this->dataType = $dataType;
    }
}