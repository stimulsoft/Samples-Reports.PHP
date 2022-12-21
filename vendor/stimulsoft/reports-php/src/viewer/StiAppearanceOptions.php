<?php

namespace Stimulsoft\Viewer;

use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the viewer appearance. */
class StiAppearanceOptions extends StiComponentOptions
{
    protected $enums = [
        'pageAlignment', 'parametersPanelPosition', 'interfaceType', 'chartRenderType', 'reportDisplayMode',
        'datePickerFirstDayOfWeek', 'theme'
    ];

    /** Gets or sets the background color of the viewer. */
    public $backgroundColor = 'white';

    /** Gets or sets a color of the report page border. */
    public $pageBorderColor = 'gray';

    /** Gets or sets a value which controls of output objects in the right to left mode. */
    public $rightToLeft = false;

    /** Gets or sets a value which indicates which indicates that the viewer is displayed in full screen mode. */
    public $fullScreenMode = false;

    /** Gets or sets a value which indicates that the viewer will show the report area with scrollbars. */
    public $scrollbarsMode = false;

    /** Gets or sets a browser window to open links from the report. */
    public $openLinksWindow = '_blank';

    /** Gets or sets a browser window to open the exported report. */
    public $openExportedReportWindow = '_blank';

    /** Gets or sets a value which indicates that show or hide tooltips. */
    public $showTooltips = true;

    /** Gets or sets a value which indicates that show or hide the help link in tooltips. */
    public $showTooltipsHelp = true;

    /** Gets or sets a value which indicates that show or hide the help button in dialogs. */
    public $showDialogsHelp = true;

    /** Gets or sets the alignment of the viewer page. */
    public $pageAlignment = StiContentAlignment::DefaultValue;

    /** Gets or sets a value which indicates that the shadow of the page will be displayed in the viewer. */
    public $showPageShadow = false;

    /** Gets or sets a value which allows printing report bookmarks. */
    public $bookmarksPrint = false;

    /** Gets or sets a width of the bookmarks tree in the viewer. */
    public $bookmarksTreeWidth = 180;

    /** Gets or sets a position of the parameters panel. */
    public $parametersPanelPosition = StiParametersPanelPosition::FromReport;

    /** Gets or sets a max height of parameters panel in the viewer. */
    public $parametersPanelMaxHeight = 300;

    /** Gets or sets a count columns in parameters panel. */
    public $parametersPanelColumnsCount = 2;

    /** Gets or sets a date format for datetime parameters in parameters panel. The default is the client browser date format. */
    public $parametersPanelDateFormat = '';

    /** Gets or sets a value which indicates that variable items will be sorted. */
    public $parametersPanelSortDataItems = false;

    /** Gets or sets the type of the viewer interface. */
    public $interfaceType = StiInterfaceType::Auto;

    /** Gets or sets the type of the chart in the viewer. */
    public $chartRenderType = StiChartRenderType::AnimatedVector;

    /** Gets or sets a method how the viewer will show a report. */
    public $reportDisplayMode = StiHtmlExportMode::FromReport;

    /** Gets or sets the first day of week in the date picker */
    public $datePickerFirstDayOfWeek = StiFirstDayOfWeek::Auto;

    /** Gets or sets a value, which indicates that the current day will be included in the ranges of the date picker. */
    public $datePickerIncludeCurrentDayForRanges = false;

    /** Gets or sets a value which allows touch zoom in the viewer. */
    public $allowTouchZoom = true;

    /** Gets or sets a value which indicates that allows mobile mode of the viewer interface. */
    public $allowMobileMode = true;

    /** Gets or sets a value which indicates that if a report contains several pages, then they will be combined in preview. */
    public $combineReportPages = false;

    /** Gets or sets the current visual theme which is used for drawing visual elements of the viewer. */
    public $theme = StiViewerTheme::Office2022WhiteBlue;
}