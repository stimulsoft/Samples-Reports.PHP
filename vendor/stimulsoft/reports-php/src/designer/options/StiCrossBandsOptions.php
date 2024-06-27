<?php

namespace Stimulsoft\Designer;

use Stimulsoft\StiComponent;
use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the cross-bands. */
class StiCrossBandsOptions extends StiComponentOptions
{

### Options

    /** @var bool Gets or sets a visibility of the CrossTab item in the crossbands menu of the designer. */
    public $showCrossTab = true;

    /** @var bool Gets or sets a visibility of the CrossGroupHeaderBand item in the crossbands menu of the designer. */
    public $showCrossGroupHeaderBand = true;

    /** @var bool Gets or sets a visibility of the CrossGroupFooterBand item in the crossbands menu of the designer. */
    public $showCrossGroupFooterBand = true;

    /** @var bool Gets or sets a visibility of the CrossHeaderBand item in the crossbands menu of the designer. */
    public $showCrossHeaderBand = true;

    /** @var bool Gets or sets a visibility of the CrossFooterBand item in the crossbands menu of the designer. */
    public $showCrossFooterBand = true;

    /** @var bool Gets or sets a visibility of the CrossDataBand item in the crossbands menu of the designer. */
    public $showCrossDataBand = true;


### Helpers

    public function setComponent(StiComponent $component)
    {
        parent::setComponent($component);

        $this->id .= '.crossBands';
    }
}