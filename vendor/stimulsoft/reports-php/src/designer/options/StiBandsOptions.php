<?php

namespace Stimulsoft\Designer;

use Stimulsoft\StiComponent;
use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the bands. */
class StiBandsOptions extends StiComponentOptions
{

### Options

    /** @var bool Gets or sets a visibility of the ReportTitleBand item in the bands menu of the designer. */
    public $showReportTitleBand = true;

    /** @var bool Gets or sets a visibility of the ReportSummaryBand item in the bands menu of the designer. */
    public $showReportSummaryBand = true;

    /** @var bool Gets or sets a visibility of the PageHeaderBand item in the bands menu of the designer. */
    public $showPageHeaderBand = true;

    /** @var bool Gets or sets a visibility of the PageFooterBand item in the bands menu of the designer. */
    public $showPageFooterBand = true;

    /** @var bool Gets or sets a visibility of the GroupHeaderBand item in the bands menu of the designer. */
    public $showGroupHeaderBand = true;

    /** @var bool Gets or sets a visibility of the GroupFooterBand item in the bands menu of the designer. */
    public $showGroupFooterBand = true;

    /** @var bool Gets or sets a visibility of the HeaderBand item in the bands menu of the designer. */
    public $showHeaderBand = true;

    /** @var bool Gets or sets a visibility of the FooterBand item in the bands menu of the designer. */
    public $showFooterBand = true;

    /** @var bool Gets or sets a visibility of the ColumnHeaderBand item in the bands menu of the designer. */
    public $showColumnHeaderBand = true;

    /** @var bool Gets or sets a visibility of the ColumnFooterBand item in the bands menu of the designer. */
    public $showColumnFooterBand = true;

    /** @var bool Gets or sets a visibility of the DataBand item in the bands menu of the designer. */
    public $showDataBand = true;

    /** @var bool Gets or sets a visibility of the HierarchicalBand item in the bands menu of the designer. */
    public $showHierarchicalBand = true;

    /** @var bool Gets or sets a visibility of the ChildBand item in the bands menu of the designer. */
    public $showChildBand = true;

    /** @var bool Gets or sets a visibility of the EmptyBand item in the bands menu of the designer. */
    public $showEmptyBand = true;

    /** @var bool Gets or sets a visibility of the OverlayBand item in the bands menu of the designer. */
    public $showOverlayBand = true;

    /** @var bool Gets or sets a visibility of the Table item in the bands menu of the designer. */
    public $showTable = true;

    /** @var bool Gets or sets a visibility of the TableOfContents item in the Bands menu of the designer. */
    public $showTableOfContents = true;


### Helpers

    public function setComponent(StiComponent $component)
    {
        parent::setComponent($component);

        $this->id .= '.bands';
    }
}