<?php

namespace Stimulsoft\Designer;

use Stimulsoft\StiComponent;
use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the components. */
class StiReportComponentsOptions extends StiComponentOptions
{

### Options

    /** @var bool Gets or sets a visibility of the Text item in the components menu of the designer. */
    public $showText = true;

    /** @var bool Gets or sets a visibility of the TextInCells item in the components menu of the designer. */
    public $showTextInCells = true;

    /** @var bool Gets or sets a visibility of the RichText item in the components menu of the designer. */
    public $showRichText = false;

    /** @var bool Gets or sets a visibility of the Image item in the components menu of the designer. */
    public $showImage = true;

    /** @var bool Gets or sets a visibility of the BarCode item in the components menu of the designer. */
    public $showBarCode = true;

    /** @var bool Gets or sets a visibility of the Shape item in the components menu of the designer. */
    public $showShape = true;

    /** @var bool Gets or sets a visibility of the Panel item in the components menu of the designer. */
    public $showPanel = true;

    /** @var bool Gets or sets a visibility of the Clone item in the components menu of the designer. */
    public $showClone = true;

    /** @var bool Gets or sets a visibility of the CheckBox item in the components menu of the designer. */
    public $showCheckBox = true;

    /** @var bool Gets or sets a visibility of the SubReport item in the components menu of the designer. */
    public $showSubReport = true;

    /** @var bool Gets or sets a visibility of the ZipCode item in the components menu of the designer. */
    public $showZipCode = false;

    /** @var bool Gets or sets a visibility of the Chart item in the components menu of the designer. */
    public $showChart = true;

    /** @var bool Gets or sets a visibility of the Gauge item in the components menu of the designer. */
    public $showGauge = true;

    /** @var bool Gets or sets a visibility of the Sparkline item in the components menu of the designer. */
    public $showSparkline = true;

    /** @var bool Gets or sets a visibility of the MathFormula item in the Components menu of the designer. */
    public $showMathFormula = false;

    /** @var bool Gets or sets a visibility of the Map item in the Components menu of the designer. */
    public $showMap = true;

    /** @var bool Gets or sets a visibility of the Electronic Signature item in the Components menu of the designer. */
    public $showElectronicSignature = true;

    /** @var bool Gets or sets a visibility of the PdfDigitalSignature item in the Components menu of the designer. */
    public $showPdfDigitalSignature = true;

    /** @var bool Gets or sets a visibility of the Horizontal Line Primitive item in the Components menu of the designer. */
    public $showHorizontalLinePrimitive = true;

    /** @var bool Gets or sets a visibility of the Vertical Line Primitive item in the Components menu of the designer. */
    public $showVerticalLinePrimitive = true;

    /** @var bool Gets or sets a visibility of the Rectangle Primitive item in the Components menu of the designer. */
    public $showRectanglePrimitive = true;

    /** @var bool Gets or sets a visibility of the Rounded Rectangle Primitive item in the Components menu of the designer. */
    public $showRoundedRectanglePrimitive = true;


### Helpers

    public function setComponent(StiComponent $component)
    {
        parent::setComponent($component);

        $this->id .= '.components';
    }
}