<?php

namespace Stimulsoft\Viewer;

class Color extends \Stimulsoft\OptionsBase
{
	protected static $validFields = array(
		'name' => 'string',
		'_a' => 'integer',
		'_r' => 'integer',
		'_g' => 'integer',
		'_b' => 'integer',
	);

	protected static $defaults = array(
		'name' => 'White',
		'_a' => 255,
		'_r' => 255,
		'_g' => 255,
		'_b' => 255,
	);
}
