<?php

namespace Stimulsoft\Designer;

class Components extends \Stimulsoft\OptionsBase
{
	protected static $validFields = array(
		'showBarCode' => 'boolean',
		'showChart' => 'boolean',
		'showCheckBox' => 'boolean',
		'showClone' => 'boolean',
		'showGauge' => 'boolean',
		'showImage' => 'boolean',
		'showMathFormula' => 'boolean',
		'showPanel' => 'boolean',
		'showRichText' => 'boolean',
		'showShape' => 'boolean',
		'showSparkline' => 'boolean',
		'showSubReport' => 'boolean',
		'showText' => 'boolean',
		'showTextInCells' => 'boolean',
		'showZipCode' => 'boolean',
	);

	protected static $defaults = array(
		'showBarCode' => true,
		'showChart' => true,
		'showCheckBox' => true,
		'showClone' => true,
		'showGauge' => true,
		'showImage' => true,
		'showMathFormula' => false,
		'showPanel' => true,
		'showRichText' => false,
		'showShape' => true,
		'showSparkline' => true,
		'showSubReport' => true,
		'showText' => true,
		'showTextInCells' => true,
		'showZipCode' => false,
	);
}
