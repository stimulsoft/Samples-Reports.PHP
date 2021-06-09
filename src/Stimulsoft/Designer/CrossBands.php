<?php

namespace Stimulsoft\Designer;

class CrossBands extends \Stimulsoft\OptionsBase
{
	protected static $validFields = array(
		'showCrossDataBand' => 'boolean',
		'showCrossFooterBand' => 'boolean',
		'showCrossGroupFooterBand' => 'boolean',
		'showCrossGroupHeaderBand' => 'boolean',
		'showCrossHeaderBand' => 'boolean',
		'showCrossTab' => 'boolean',
	);

	protected static $defaults = array(
		'showCrossDataBand' => true,
		'showCrossFooterBand' => true,
		'showCrossGroupFooterBand' => true,
		'showCrossGroupHeaderBand' => true,
		'showCrossHeaderBand' => true,
		'showCrossTab' => true,
	);
}
