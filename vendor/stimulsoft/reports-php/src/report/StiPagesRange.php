<?php

namespace Stimulsoft\Report;

use Stimulsoft\Report\Enums\StiRangeType;
use Stimulsoft\StiJsElement;

class StiPagesRange extends StiJsElement
{
    public $rangeType = StiRangeType::All;
    public $pageRanges = '';
    public $currentPage = 0;

    public function getHtml($newObject = true): string
    {
        $result = $newObject ? "let $this->id = new Stimulsoft.Report.StiPagesRange();\n" : '';
        if ($this->rangeType != StiRangeType::All) {
            $result .= "$this->id.rangeType = $this->rangeType;\n";

            if (!is_null($this->pageRanges) && strlen($this->pageRanges) > 0)
                $result .= "$this->id.pageRanges = '$this->pageRanges';\n";

            if ($this->currentPage > 0)
                $result .= "$this->id.currentPage = $this->currentPage;\n";
        }

        return parent::getHtml() . $result;
    }

    public function __construct($rangeType = StiRangeType::All, $pageRanges = '', $currentPage = 0)
    {
        $this->id = 'pagesRange';
        $this->rangeType = $rangeType;
        $this->pageRanges = $pageRanges;
        $this->currentPage = $currentPage;
    }
}