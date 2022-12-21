<?php

namespace Stimulsoft\Viewer;

use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the viewer toolbar. */
class StiToolbarOptions extends StiComponentOptions
{
    protected $enums = [
        'displayMode', 'alignment', 'printDestination', 'viewMode', 'zoom', 'showMenuMode'
    ];

    /** Gets or sets a value which indicates that toolbar will be shown in the viewer. */
    public $visible = true;

    /** Gets or sets the display mode of the toolbar - simple or separated into upper and lower parts. */
    public $displayMode = StiToolbarDisplayMode::Simple;

    /** Gets or sets a color of the toolbar background. The default value is the theme color. */
    public $backgroundColor = 'transparent';

    /** Gets or sets a color of the toolbar border. The default value is the theme color. */
    public $borderColor = 'transparent';

    /** Gets or sets a color of the toolbar texts. */
    public $fontColor = 'transparent';

    /** Gets or sets a value which indicates which font family will be used for drawing texts in the viewer. */
    public $fontFamily = 'Arial';

    /** Gets or sets the alignment of the viewer toolbar. */
    public $alignment = StiContentAlignment::DefaultValue;

    /** Gets or sets a value which allows displaying or hiding toolbar buttons captions. */
    public $showButtonCaptions = true;

    /** Gets or sets a visibility of the Print button in the toolbar of the viewer. */
    public $showPrintButton = true;

    /** Gets or sets a visibility of the Open button in the toolbar of the viewer. */
    public $showOpenButton = true;

    /** Gets or sets a visibility of the Save button in the toolbar of the viewer. */
    public $showSaveButton = true;

    /** Gets or sets a visibility of the Send Email button in the toolbar of the viewer. */
    public $showSendEmailButton = false;

    /** Gets or sets a visibility of the Find button in the toolbar of the viewer. */
    public $showFindButton = true;

    /** Gets or sets a visibility of the Bookmarks button in the toolbar of the viewer. */
    public $showBookmarksButton = true;

    /** Gets or sets a visibility of the Parameters button in the toolbar of the viewer. */
    public $showParametersButton = true;

    /** Gets or sets a visibility of the Resources button in the toolbar of the viewer. */
    public $showResourcesButton = true;

    /** Gets or sets a visibility of the Editor button in the toolbar of the viewer. */
    public $showEditorButton = true;

    /** Gets or sets a visibility of the Full Screen button in the toolbar of the viewer. */
    public $showFullScreenButton = true;

    /** Gets or sets a visibility of the Refresh button in the toolbar of the viewer. */
    public $showRefreshButton = true;

    /** Gets or sets a visibility of the First Page button in the toolbar of the viewer. */
    public $showFirstPageButton = true;

    /** Gets or sets a visibility of the Prev Page button in the toolbar of the viewer. */
    public $showPreviousPageButton = true;

    /** Gets or sets a visibility of the current page control in the toolbar of the viewer. */
    public $showCurrentPageControl = true;

    /** Gets or sets a visibility of the Next Page button in the toolbar of the viewer. */
    public $showNextPageButton = true;

    /** Gets or sets a visibility of the Last Page button in the toolbar of the viewer. */
    public $showLastPageButton = true;

    /** Gets or sets a visibility of the Zoom control in the toolbar of the viewer. */
    public $showZoomButton = true;

    /** Gets or sets a visibility of the View Mode button in the toolbar of the viewer. */
    public $showViewModeButton = true;

    /** Gets or sets a visibility of the Design button in the toolbar of the viewer. */
    public $showDesignButton = false;

    /** Gets or sets a visibility of the About button in the toolbar of the viewer. */
    public $showAboutButton = true;

    /** Gets or sets a visibility of the Pin button in the toolbar of the viewer in mobile mode. */
    public $showPinToolbarButton = true;

    /** Gets or sets the default mode of the report print destination. */
    public $printDestination = StiPrintDestination::DefaultValue;

    /** Gets or sets the mode of showing a report in the viewer - one page or the whole report. */
    public $viewMode = StiWebViewMode::SinglePage;

    /** Gets or sets the report showing zoom. The default value is 100. */
    public $zoom = 100;

    /** Gets or sets a value which indicates that menu animation is enabled. */
    public $menuAnimation = true;

    /** Gets or sets the mode that shows menu of the viewer. */
    public $showMenuMode = StiShowMenuMode::Click;

    /** Gets or sets a value which allows automatically hide the viewer toolbar in mobile mode. */
    public $autoHide = false;
}