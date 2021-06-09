<?php

namespace Stimulsoft\Designer;

class StiDesignerOptions extends \Stimulsoft\OptionsBase
{
	protected static $validFields = array(
		'appearance' => 'Stimulsoft\\Designer\\Appearance',
		'bands' => 'Stimulsoft\\Designer\\Bands',
		'components' => 'Stimulsoft\\Designer\\Components',
		'crossBands' => 'Stimulsoft\\Designer\\CrossBands',
		'dashboardElements' => 'Stimulsoft\\Designer\\DashboardElements',
		'dictionary' => 'Stimulsoft\\Designer\\Dictionary',
		'height' => 'string',
		'mobileDesignerId' => 'string',
		'productVersion' => 'string',
		'toolbar' => 'Stimulsoft\\Designer\\Toolbar',
		'viewerOptions' => 'Stimulsoft\\Viewer\\StiViewerOptions',
		'width' => 'string',
	);

	protected static $defaults = array(
		'height' => '800px',
		'mobileDesignerId' => '',
		'productVersion' => '2021.3.2 from 27 May 2021',
		'width' => '100%',
	);

	/**
	 * Construct the options generation class.
	 *
	 * @param string $name of the JavaScript variable you want to create and initialize.
	 */
	public function __construct($name = '')
	{
		$this->varName = $name;
		$this->className = \str_replace('\\', '.', __CLASS__);

		parent::__construct();
	}
}
