<?php

namespace Stimulsoft\Designer;

class Bands extends \Stimulsoft\OptionsBase
{
	protected static $validFields = array(
		'showChildBand' => 'boolean',
		'showColumnFooterBand' => 'boolean',
		'showColumnHeaderBand' => 'boolean',
		'showDataBand' => 'boolean',
		'showEmptyBand' => 'boolean',
		'showFooterBand' => 'boolean',
		'showGroupFooterBand' => 'boolean',
		'showGroupHeaderBand' => 'boolean',
		'showHeaderBand' => 'boolean',
		'showHierarchicalBand' => 'boolean',
		'showOverlayBand' => 'boolean',
		'showPageFooterBand' => 'boolean',
		'showPageHeaderBand' => 'boolean',
		'showReportSummaryBand' => 'boolean',
		'showReportTitleBand' => 'boolean',
		'showTable' => 'boolean',
		'showTableOfContents' => 'boolean',
	);

	protected static $defaults = array(
		'showChildBand' => true,
		'showColumnFooterBand' => true,
		'showColumnHeaderBand' => true,
		'showDataBand' => true,
		'showEmptyBand' => true,
		'showFooterBand' => true,
		'showGroupFooterBand' => true,
		'showGroupHeaderBand' => true,
		'showHeaderBand' => true,
		'showHierarchicalBand' => true,
		'showOverlayBand' => true,
		'showPageFooterBand' => true,
		'showPageHeaderBand' => true,
		'showReportSummaryBand' => true,
		'showReportTitleBand' => true,
		'showTable' => true,
		'showTableOfContents' => true,
	);
}
