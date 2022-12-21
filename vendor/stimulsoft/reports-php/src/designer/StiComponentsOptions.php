<?php

namespace Stimulsoft\Designer;

use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the components. */
class StiComponentsOptions extends StiComponentOptions
{
    /** Gets or sets a visibility of the Text item in the components menu of the designer. */
    public $showText = true;

    /** Gets or sets a visibility of the TextInCells item in the components menu of the designer. */
    public $showTextInCells = true;

    /** Gets or sets a visibility of the RichText item in the components menu of the designer. */
    public $showRichText = false;

    /** Gets or sets a visibility of the Image item in the components menu of the designer. */
    public $showImage = true;

    /** Gets or sets a visibility of the BarCode item in the components menu of the designer. */
    public $showBarCode = true;

    /** Gets or sets a visibility of the Shape item in the components menu of the designer. */
    public $showShape = true;

    /** Gets or sets a visibility of the Panel item in the components menu of the designer. */
    public $showPanel = true;

    /** Gets or sets a visibility of the Clone item in the components menu of the designer. */
    public $showClone = true;

    /** Gets or sets a visibility of the CheckBox item in the components menu of the designer. */
    public $showCheckBox = true;

    /** Gets or sets a visibility of the SubReport item in the components menu of the designer. */
    public $showSubReport = true;

    /** Gets or sets a visibility of the ZipCode item in the components menu of the designer. */
    public $showZipCode = false;

    /** Gets or sets a visibility of the Chart item in the components menu of the designer. */
    public $showChart = true;

    /** Gets or sets a visibility of the Gauge item in the components menu of the designer. */
    public $showGauge = true;

    /** Gets or sets a visibility of the Sparkline item in the components menu of the designer. */
    public $showSparkline = true;

    /** Gets or sets a visibility of the MathFormula item in the Components menu of the designer. */
    public $showMathFormula = false;

    /** Gets or sets a visibility of the Map item in the Components menu of the designer. */
    public $showMap = true;
}