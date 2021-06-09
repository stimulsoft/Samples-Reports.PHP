<?php

namespace Stimulsoft\Viewer;

class StiViewerOptions extends \Stimulsoft\OptionsBase
{
	protected static $validFields = array(
		'actions' => 'Stimulsoft\\Viewer\\Actions',
		'appearance' => 'Stimulsoft\\Viewer\\Appearance',
		'email' => 'Stimulsoft\\Viewer\\Email',
		'exports' => 'Stimulsoft\\Viewer\\Exports',
		'height' => 'string',
		'productVersion' => 'string',
		'reportDesignerMode' => 'boolean',
		'requestResourcesUrl' => 'string',
		'requestStylesUrl' => 'string',
		'toolbar' => 'Stimulsoft\\Viewer\\Toolbar',
		'viewerId' => 'string',
		'width' => 'string',
	);

	protected static $defaults = array(
		'height' => '',
		'productVersion' => '2021.3.2 from 27 May 2021',
		'reportDesignerMode' => false,
		'requestResourcesUrl' => '',
		'requestStylesUrl' => '',
		'viewerId' => '',
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
