<?php

namespace Stimulsoft\Report;

use Stimulsoft\StiHtmlComponent;

class StiPagesRange extends StiHtmlComponent
{
    public $rangeType = StiRangeType::All;
    public $pageRanges = '';
    public $currentPage = 0;

    /** Get the HTML representation of the component. */
    public function getHtml()
    {
        $result = "let $this->id = new Stimulsoft.Report.StiPagesRange();\n";
        if ($this->rangeType != StiRangeType::All) {
            $result .= "$this->id.rangeType = $this->rangeType;\n";

            if (!is_null($this->pageRanges) && strlen($this->pageRanges) > 0)
                $result .= "$this->id.pageRanges = '$this->pageRanges';\n";

            if ($this->currentPage > 0)
                $result .= "$this->id.currentPage = $this->currentPage;\n";
        }

        $this->isHtmlRendered = true;
        return $result;
    }

    public function __construct($rangeType = StiRangeType::All, $pageRanges = '', $currentPage = 0)
    {
        $this->id = 'pagesRange';

        $this->rangeType = $rangeType;
        $this->pageRanges = $pageRanges;
        $this->currentPage = $currentPage;
    }
}