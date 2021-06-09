<?php

namespace Stimulsoft\Designer;

class Appearance extends \Stimulsoft\OptionsBase
{
	protected static $validFields = array(
		'_showLocalization' => 'integer',
		'_zoom' => 'integer',
		'allowChangeWindowTitle' => 'boolean',
		'allowWordWrapTextEditors' => 'boolean',
		'datePickerFirstDayOfWeek' => 'integer',
		'defaultUnit' => 'integer',
		'fullScreenMode' => 'boolean',
		'interfaceType' => 'integer',
		'maximizeAfterCreating' => 'boolean',
		'propertiesGridPosition' => 'integer',
		'showAnimation' => 'boolean',
		'showDialogsHelp' => 'boolean',
		'showPropertiesGrid' => 'boolean',
		'showReportTree' => 'boolean',
		'showSaveDialog' => 'boolean',
		'showSystemFonts' => 'boolean',
		'showTooltips' => 'boolean',
		'showTooltipsHelp' => 'boolean',
		'undoMaxLevel' => 'integer',
		'wizardTypeRunningAfterLoad' => 'integer',
	);

	protected static $defaults = array(
		'_showLocalization' => -1,
		'_zoom' => 100,
		'allowChangeWindowTitle' => true,
		'allowWordWrapTextEditors' => true,
		'datePickerFirstDayOfWeek' => 0,
		'defaultUnit' => 0,
		'fullScreenMode' => true,
		'interfaceType' => 0,
		'maximizeAfterCreating' => false,
		'propertiesGridPosition' => 0,
		'showAnimation' => true,
		'showDialogsHelp' => true,
		'showPropertiesGrid' => true,
		'showReportTree' => true,
		'showSaveDialog' => true,
		'showSystemFonts' => true,
		'showTooltips' => true,
		'showTooltipsHelp' => true,
		'undoMaxLevel' => 6,
		'wizardTypeRunningAfterLoad' => 0,
	);
}
