<?php

namespace Stimulsoft\Enums;

use Stimulsoft\StiFunctions;

class StiDataType
{
    const JavaScript = "text/javascript";
    const JSON = "application/json";
    const XML = "application/xml";
    const HTML = "text/html";
    const CSV = "text/csv";
    const Text = "text/plain";


### Helpers

    public static function getValues(): array
    {
        return StiFunctions::getConstants("Stimulsoft\Enums\StiDataType");
    }
}