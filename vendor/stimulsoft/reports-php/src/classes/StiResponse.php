<?php

namespace Stimulsoft;

use Stimulsoft\Enums\StiDataType;

class StiResponse extends StiBaseResponse
{

### Helpers

    /**
     * Returns the mime-type for the handler response.
     */
    public function getMimeType(): string
    {
        if ($this->result instanceof StiFileResult) {
            $types = StiDataType::getValues();
            if (in_array($this->result->dataType, $types))
                return $this->result->dataType;
        }

        return parent::getMimeType();
    }

    /**
     * Returns the handler response as a byte string. When using encryption, the response will be encrypted and encoded into a Base64 string.
     */
    public function getData(): string
    {
        if ($this->result instanceof StiFileResult)
            return $this->result->data;

        return parent::getData();
    }
}