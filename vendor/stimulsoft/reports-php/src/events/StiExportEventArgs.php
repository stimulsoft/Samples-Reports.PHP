<?php

namespace Stimulsoft;

class StiExportEventArgs extends StiEventArgs
{
    /** @var StiExportAction The current action for which the report was exported. */
    public $action;

    /** @var StiPrintAction The current print type of the report. */
    public $printAction;

    /** @var StiExportFormat The current export format of the report. */
    public $format;

    /** @var string String name of the current export format of the report. */
    public $formatName;

    /** @var object The object of all settings for the current report export. */
    public $settings;

    /** @var StiEmailSettings Settings for sending the exported report by email. */
    public $emailSettings;

    /** @var string The file name of the exported report. */
    public $fileName;

    /** @var string File extension for the current report export. */
    public $fileExtension;

    /** @var string The byte data of the exported report in the Base64 format. */
    public $data;
}