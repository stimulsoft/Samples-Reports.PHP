<?php

namespace Stimulsoft\Events;

use Stimulsoft\Enums\StiReportType;
use Stimulsoft\Export\StiExportSettings;
use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\StiComponent;
use Stimulsoft\StiHandler;
use Stimulsoft\Viewer\Enums\StiExportAction;

class StiExportEventArgs extends StiEventArgs
{

### Properties

    /** @var StiExportAction|int [enum] The current action for which the report was exported. */
    public $action;

    /** @var StiExportFormat|int [enum] The current export format of the report. */
    public $format;

    /** @var string String name of the current export format of the report. */
    public $formatName;

    /** @var StiExportSettings The object of all settings for the current report export. */
    public $settings;

    /** @var bool The flag indicates that the report will be exported in a new browser tab (true), or the file will be saved (false). */
    public $openAfterExport;

    /** @var string The file name of the exported report. */
    public $fileName;

    /** @var string The file extension for the current report export. */
    public $fileExtension;

    /** @var string The MIME type for the current report export. */
    public $mimeType;

    /** @var string The byte data of the exported report in the Base64 format. */
    public $data;

    /** @var StiReportType|int [enum] The current type of report being exported. */
    public $reportType = StiReportType::Auto;

    /** @deprecated Please use the '$args->settings' object in the 'StiEmailEventArgs' class. */
    public $emailSettings;


### Helpers

    protected function getHandler()
    {
        if ($this->sender instanceof StiHandler)
            return $this->sender;

        if ($this->sender instanceof StiComponent)
            return $this->sender->handler;

        return null;
    }

    protected function setSettings($value)
    {
        if ($value !== null && $this->format !== null && $this->reportType !== StiReportType::Auto) {
            $this->settings = StiExportFormat::getExportSettings($this->format, $this->reportType);
            $this->settings->setObject($value);
        }
    }

    protected function setProperty($name, $value)
    {
        parent::setProperty($name, $value);

        if ($name == 'format') {
            $this->fileExtension = StiExportFormat::getFileExtension($this->format);
            $this->mimeType = StiExportFormat::getMimeType($this->format);
            $this->setSettings($this->settings);
        }

        if ($name == 'settings')
            $this->setSettings($value);

        if ($name == 'reportType')
            $this->setSettings($this->settings);
    }
}