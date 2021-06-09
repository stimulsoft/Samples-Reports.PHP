<?php

namespace Stimulsoft\Viewer;

class Exports extends \Stimulsoft\OptionsBase
{
	protected static $validFields = array(
		'showExportDialog' => 'boolean',
		'showExportToCsv' => 'boolean',
		'showExportToDocument' => 'boolean',
		'showExportToExcel2007' => 'boolean',
		'showExportToHtml' => 'boolean',
		'showExportToHtml5' => 'boolean',
		'showExportToImageSvg' => 'boolean',
		'showExportToJson' => 'boolean',
		'showExportToOpenDocumentCalc' => 'boolean',
		'showExportToOpenDocumentWriter' => 'boolean',
		'showExportToPdf' => 'boolean',
		'showExportToPowerPoint' => 'boolean',
		'showExportToText' => 'boolean',
		'showExportToWord2007' => 'boolean',
		'storeExportSettings' => 'boolean',
	);

	protected static $defaults = array(
		'showExportDialog' => true,
		'showExportToCsv' => true,
		'showExportToDocument' => true,
		'showExportToExcel2007' => true,
		'showExportToHtml' => true,
		'showExportToHtml5' => true,
		'showExportToImageSvg' => true,
		'showExportToJson' => false,
		'showExportToOpenDocumentCalc' => true,
		'showExportToOpenDocumentWriter' => true,
		'showExportToPdf' => true,
		'showExportToPowerPoint' => true,
		'showExportToText' => true,
		'showExportToWord2007' => true,
		'storeExportSettings' => true,
	);
}
