<?php

namespace Stimulsoft\Designer;

use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the designer toolbar. */
class StiToolbarOptions extends StiComponentOptions
{
    /** Gets or sets a value which indicates that toolbar will be shown in the designer. */
    public $visible = true;

    /** Gets or sets a visibility of the preview button in the toolbar of the designer. */
    public $showPreviewButton = true;

    /** Gets or sets a visibility of the save button in the toolbar of the designer. */
    public $showSaveButton = false;

    /** Gets or sets a visibility of the about button in the toolbar of the designer. */
    public $showAboutButton = false;

    /** Gets or sets a visibility of the publish button in the toolbar of the designer. */
    public $showPublishButton = true;

    /** Gets or sets a visibility of the file menu of the designer. */
    public $showFileMenu = true;

    /** Gets or sets a visibility of the item New in the file menu. */
    public $showFileMenuNew = true;

    /** Gets or sets a visibility of the item Open in the file menu. */
    public $showFileMenuOpen = true;

    /** Gets or sets a visibility of the item Save in the file menu. */
    public $showFileMenuSave = true;

    /** Gets or sets a visibility of the item Save As in the file menu. */
    public $showFileMenuSaveAs = true;

    /** Gets or sets a visibility of the item Close in the file menu. */
    public $showFileMenuClose = true;

    /** Gets or sets a visibility of the item Exit in the file menu. */
    public $showFileMenuExit = false;

    /** Gets or sets a visibility of the item Report Setup in the file menu. */
    public $showFileMenuReportSetup = true;

    /** Gets or sets a visibility of the item Options in the file menu. */
    public $showFileMenuOptions = true;

    /** Gets or sets a visibility of the item Info in the file menu. */
    public $showFileMenuInfo = true;

    /** Gets or sets a visibility of the item About in the file menu. */
    public $showFileMenuAbout = true;

    /** Gets or sets a visibility of the new report button in the file menu. */
    public $showFileMenuNewReport = true;

    /** Gets or sets a visibility of the new dashboard button in the file menu. */
    public $showFileMenuNewDashboard = true;

    /** Gets or sets a visibility of the setup toolbox button in the designer.*/
    public $showSetupToolboxButton = true;

    /** Gets or sets a visibility of the new page button in the designer.*/
    public $showNewPageButton = true;

    /** Gets or sets a visibility of the new dashboard button in the designer.*/
    public $showNewDashboardButton = true;
}