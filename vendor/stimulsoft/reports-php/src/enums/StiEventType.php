<?php

namespace Stimulsoft\Enums;

use ReflectionClass;
use Stimulsoft\StiFunctions;

class StiEventType
{
    const GetResource = 'GetResource';
    const PrepareVariables = 'PrepareVariables';
    const DatabaseConnect = 'DatabaseConnect';
    const BeginProcessData = 'BeginProcessData';
    const EndProcessData = 'EndProcessData';
    const CreateReport = 'CreateReport';
    const OpenReport = 'OpenReport';
    const OpenedReport = 'OpenedReport';
    const SaveReport = 'SaveReport';
    const SaveAsReport = 'SaveAsReport';
    const PrintReport = 'PrintReport';
    const BeginExportReport = 'BeginExportReport';
    const EndExportReport = 'EndExportReport';
    const EmailReport = 'EmailReport';
    const Interaction = 'Interaction';
    const DesignReport = 'DesignReport';
    const PreviewReport = 'PreviewReport';
    const CloseReport = 'CloseReport';
    const Exit = 'Exit';


### Helpers

    public static function getValues(): array
    {
        return StiFunctions::getConstants('Stimulsoft\Enums\StiEventType');
    }
}