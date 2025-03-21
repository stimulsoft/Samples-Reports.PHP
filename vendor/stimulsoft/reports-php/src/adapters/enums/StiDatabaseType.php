<?php

namespace Stimulsoft\Enums;

use Stimulsoft\StiFunctions;

class StiDatabaseType
{
    // File
    const XML = 'XML';
    const JSON = 'JSON';
    const CSV = 'CSV';

    // SQL
    const MySQL = 'MySQL';
    const MSSQL = 'MS SQL';
    const PostgreSQL = 'PostgreSQL';
    const Firebird = 'Firebird';
    const Oracle = 'Oracle';
    const ODBC = 'ODBC';

    // NoSQL
    const MongoDB = 'MongoDB';


### Helpers

    public static function getValues(): array
    {
        return StiFunctions::getConstants('Stimulsoft\Enums\StiDatabaseType');
    }
}