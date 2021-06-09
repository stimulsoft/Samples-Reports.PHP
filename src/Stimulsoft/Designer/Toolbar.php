<?php

namespace Stimulsoft\Designer;

class Toolbar extends \Stimulsoft\OptionsBase
{
	protected static $validFields = array(
		'showAboutButton' => 'boolean',
		'showFileMenu' => 'boolean',
		'showFileMenuAbout' => 'boolean',
		'showFileMenuClose' => 'boolean',
		'showFileMenuExit' => 'boolean',
		'showFileMenuInfo' => 'boolean',
		'showFileMenuNew' => 'boolean',
		'showFileMenuNewDashboard' => 'boolean',
		'showFileMenuNewReport' => 'boolean',
		'showFileMenuOpen' => 'boolean',
		'showFileMenuOptions' => 'boolean',
		'showFileMenuReportSetup' => 'boolean',
		'showFileMenuSave' => 'boolean',
		'showFileMenuSaveAs' => 'boolean',
		'showNewDashboardButton' => 'boolean',
		'showNewPageButton' => 'boolean',
		'showPreviewButton' => 'boolean',
		'showPublishButton' => 'boolean',
		'showSaveButton' => 'boolean',
		'showSetupToolboxButton' => 'boolean',
		'visible' => 'boolean',
	);

	protected static $defaults = array(
		'showAboutButton' => false,
		'showFileMenu' => true,
		'showFileMenuAbout' => true,
		'showFileMenuClose' => true,
		'showFileMenuExit' => false,
		'showFileMenuInfo' => true,
		'showFileMenuNew' => true,
		'showFileMenuNewDashboard' => true,
		'showFileMenuNewReport' => true,
		'showFileMenuOpen' => true,
		'showFileMenuOptions' => true,
		'showFileMenuReportSetup' => true,
		'showFileMenuSave' => true,
		'showFileMenuSaveAs' => true,
		'showNewDashboardButton' => true,
		'showNewPageButton' => true,
		'showPreviewButton' => true,
		'showPublishButton' => true,
		'showSaveButton' => true,
		'showSetupToolboxButton' => true,
		'visible' => true,
	);
}
