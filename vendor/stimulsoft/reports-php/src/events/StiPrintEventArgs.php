<?php

namespace Stimulsoft\Events;

use Stimulsoft\Export\StiHtmlExportSettings;
use Stimulsoft\Export\StiPdfExportSettings;
use Stimulsoft\Report\StiPagesRange;
use Stimulsoft\Viewer\Enums\StiPrintAction;

class StiPrintEventArgs extends StiReportEventArgs
{

### Properties

    /** @var StiPrintAction|string [enum] The current print type of the report. */
    public $printAction;

    /**
     * @var StiPdfExportSettings|StiHtmlExportSettings|null The export settings used to prepare the report for printing:
     * PDF settings for the 'PrintPdf' action, HTML settings for other actions. Allowed to change the properties of the settings object.
     * It is null if the client-side scripts do not support passing the export settings.
     */
    public $exportSettings;

    /**
     * @var StiPagesRange The page range to print the report.
     * If both this page range and the page range of the export settings are changed, this page range takes priority.
     */
    public $pageRange;


### Helpers

    protected function setExportSettings($value)
    {
        if ($value !== null && $this->printAction !== null) {
            $this->exportSettings = $this->printAction == StiPrintAction::PrintPdf
                ? new StiPdfExportSettings()
                : new StiHtmlExportSettings();

            $this->exportSettings->setObject($value);
        }
    }

    protected function setProperty(string $name, $value)
    {
        parent::setProperty($name, $value);

        if ($name == 'printAction')
            $this->setExportSettings($this->exportSettings);

        if ($name == 'exportSettings')
            $this->setExportSettings($value);

        if ($name == 'pageRange' && $value !== null)
            $this->pageRange = new StiPagesRange($value->rangeType, $value->pageRanges, $value->currentPage);
    }
}