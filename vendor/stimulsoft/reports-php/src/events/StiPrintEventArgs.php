<?php

namespace Stimulsoft\Events;

use Stimulsoft\Report\StiPagesRange;
use Stimulsoft\Viewer\Enums\StiPrintAction;

class StiPrintEventArgs extends StiReportEventArgs
{

### Properties

    /** @var StiPrintAction|string [enum] The current print type of the report. */
    public $printAction;

    /** @var StiPagesRange The page range to print the report. */
    public $pageRange;


### Helpers

    protected function setProperty($name, $value)
    {
        parent::setProperty($name, $value);

        if ($name == 'pageRange' && $value !== null)
            $this->pageRange = new StiPagesRange($value->rangeType, $value->pageRanges, $value->currentPage);
    }
}