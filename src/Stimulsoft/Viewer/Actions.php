<?php

namespace Stimulsoft\Viewer;

class Actions extends \Stimulsoft\OptionsBase
{
	protected static $validFields = array(
		'emailReport' => 'integer',
		'exportReport' => 'integer',
	);

	protected static $defaults = array(
		'emailReport' => 2,
		'exportReport' => 1,
	);
}
