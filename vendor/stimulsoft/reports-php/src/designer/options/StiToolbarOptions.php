<?php

namespace Stimulsoft\Designer;

use Stimulsoft\StiComponent;
use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the designer toolbar. */
class StiToolbarOptions extends StiComponentOptions
{

### Options

    /** @var bool Gets or sets a value which indicates that toolbar will be shown in the designer. */
    public $visible = true;

    /** @var bool Gets or sets a visibility of the preview button in the toolbar of the designer. */
    public $showPreviewButton = true;

    /** @var bool Gets or sets a visibility of the save button in the toolbar of the designer. */
    public $showSaveButton = false;

    /** @var bool Gets or sets a visibility of the about button in the toolbar of the designer. */
    public $showAboutButton = false;

    /** @var bool Gets or sets a visibility of the file menu of the designer. */
    public $showFileMenu = true;

    /** @var bool Gets or sets a visibility of the item New in the file menu. */
    public $showFileMenuNew = true;

    /** @var bool Gets or sets a visibility of the item Open in the file menu. */
    public $showFileMenuOpen = true;

    /** @var bool Gets or sets a visibility of the item Save in the file menu. */
    public $showFileMenuSave = true;

    /** @var bool Gets or sets a visibility of the item Save As in the file menu. */
    public $showFileMenuSaveAs = true;

    /** @var bool Gets or sets a visibility of the item Close in the file menu. */
    public $showFileMenuClose = true;

    /** @var bool Gets or sets a visibility of the item Exit in the file menu. */
    public $showFileMenuExit = false;

    /** @var bool Gets or sets a visibility of the item Report Setup in the file menu. */
    public $showFileMenuReportSetup = true;

    /** @var bool Gets or sets a visibility of the item Options in the file menu. */
    public $showFileMenuOptions = true;

    /** @var bool Gets or sets a visibility of the item Info in the file menu. */
    public $showFileMenuInfo = true;

    /** @var bool Gets or sets a visibility of the item About in the file menu. */
    public $showFileMenuAbout = true;

    /** @var bool Gets or sets a visibility of the new report button in the file menu. */
    public $showFileMenuNewReport = true;

    /** @var bool Gets or sets a visibility of the new dashboard button in the file menu. */
    public $showFileMenuNewDashboard = true;

    /** @var bool Gets or sets a visibility of the setup toolbox button in the designer.*/
    public $showSetupToolboxButton = true;

    /** @var bool Gets or sets a visibility of the new page button in the designer.*/
    public $showNewPageButton = true;

    /** @var bool Gets or sets a visibility of the new dashboard button in the designer.*/
    public $showNewDashboardButton = true;


### Helpers

    public function setComponent(StiComponent $component)
    {
        parent::setComponent($component);

        $this->id .= '.toolbar';
    }
}