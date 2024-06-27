<?php

namespace Stimulsoft\Designer;

use Stimulsoft\StiComponent;
use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the dashboardElements. */
class StiDashboardElementsOptions extends StiComponentOptions
{

### Options

    /** @var bool Gets or sets a visibility of the TableElement item in the designer. */
    public $showTableElement = true;

    /** @var bool Gets or sets a visibility of the CardsElement item in the designer. */
    public $showCardsElement = true;

    /** @var bool Gets or sets a visibility of the ChartElement item in the designer. */
    public $showChartElement = true;

    /** @var bool Gets or sets a visibility of the GaugeElement item in the designer. */
    public $showGaugeElement = true;

    /** @var bool Gets or sets a visibility of the PivotTableElement item in the designer. */
    public $howPivotTableElement = true;

    /** @var bool Gets or sets a visibility of the IndicatorElement item in the designer. */
    public $showIndicatorElement = true;

    /** @var bool Gets or sets a visibility of the ProgressElement item in the designer. */
    public $showProgressElement = true;

    /** @var bool Gets or sets a visibility of the RegionMapElement item in the designer. */
    public $showRegionMapElement = true;

    /** @var bool Gets or sets a visibility of the OnlineMapElement item in the designer. */
    public $showOnlineMapElement = true;

    /** @var bool Gets or sets a visibility of the ImageElement item in the designer. */
    public $showImageElement = true;

    /** @var bool Gets or sets a visibility of the WebContentElement item in the designer. */
    public $showWebContentElement = true;

    /** @var bool Gets or sets a visibility of the TextElement item in the designer. */
    public $showTextElement = true;

    /** @var bool Gets or sets a visibility of the PanelElement item in the designer. */
    public $showPanelElement = true;

    /** @var bool Gets or sets a visibility of the ShapeElement item in the designer. */
    public $showShapeElement = true;

    /** @var bool Gets or sets a visibility of the ListBoxElement item in the designer. */
    public $showListBoxElement = true;

    /** @var bool Gets or sets a visibility of the ComboBoxElement item in the designer. */
    public $showComboBoxElement = true;

    /** @var bool Gets or sets a visibility of the TreeViewElement item in the designer. */
    public $showTreeViewElement = true;

    /** @var bool Gets or sets a visibility of the TreeViewBoxElement item in the designer. */
    public $showTreeViewBoxElement = true;

    /** @var bool Gets or sets a visibility of the DatePickerElement item in the designer. */
    public $showDatePickerElement = true;

    /** @var bool Gets or sets a visibility of the ButtonElement item in the designer. */
    public $showButtonElement = true;

    /** @var bool Gets or sets a visibility of the NumberBoxElement item in the designer. */
    public $showNumberBoxElement = true;


### Helpers

    public function setComponent(StiComponent $component)
    {
        parent::setComponent($component);

        $this->id .= '.dashboardElements';
    }
}