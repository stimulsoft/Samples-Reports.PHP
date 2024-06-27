<?php

namespace Stimulsoft\Viewer;

use Stimulsoft\StiComponent;
use Stimulsoft\StiComponentOptions;
use Stimulsoft\Viewer\Enums\StiChartRenderType;
use Stimulsoft\Viewer\Enums\StiContentAlignment;
use Stimulsoft\Viewer\Enums\StiFirstDayOfWeek;
use Stimulsoft\Viewer\Enums\StiHtmlExportMode;
use Stimulsoft\Viewer\Enums\StiInterfaceType;
use Stimulsoft\Viewer\Enums\StiParametersPanelPosition;
use Stimulsoft\Viewer\Enums\StiViewerTheme;
use Stimulsoft\Viewer\Enums\StiWebUIIconSet;

/** A class which controls settings of the viewer appearance. */
class StiAppearanceOptions extends StiComponentOptions
{

### Options

    /** @var string Gets or sets the background color of the viewer. */
    public $backgroundColor = 'white';

    /** @var string Gets or sets a color of the report page border. */
    public $pageBorderColor = 'gray';

    /** @var bool Gets or sets a value which controls of output objects in the right to left mode. */
    public $rightToLeft = false;

    /** @var bool Gets or sets a value which indicates which indicates that the viewer is displayed in full screen mode. */
    public $fullScreenMode = false;

    /** @var bool Gets or sets a value which indicates that the viewer will show the report area with scrollbars. */
    public $scrollbarsMode = false;

    /** @var string Gets or sets a browser window to open links from the report. */
    public $openLinksWindow = '_blank';

    /** @var string Gets or sets a browser window to open the exported report. */
    public $openExportedReportWindow = '_blank';

    /** @var bool Gets or sets a value which indicates that show or hide tooltips. */
    public $showTooltips = true;

    /** @var bool Gets or sets a value which indicates that show or hide the help link in tooltips. */
    public $showTooltipsHelp = true;

    /** @var bool Gets or sets a value which indicates that show or hide the help button in dialogs. */
    public $showDialogsHelp = true;

    /** @var StiContentAlignment [enum] Gets or sets the alignment of the viewer page. */
    public $pageAlignment = StiContentAlignment::Center;

    /** @var bool Gets or sets a value which indicates that the shadow of the page will be displayed in the viewer. */
    public $showPageShadow = false;

    /** @var bool Gets or sets a value which allows printing report bookmarks. */
    public $bookmarksPrint = false;

    /** @var int Gets or sets a width of the bookmarks tree in the viewer. */
    public $bookmarksTreeWidth = 180;

    /** @var StiParametersPanelPosition [enum] Gets or sets a position of the parameters panel. */
    public $parametersPanelPosition = StiParametersPanelPosition::FromReport;

    /** @var int Gets or sets a max height of parameters panel in the viewer. */
    public $parametersPanelMaxHeight = 300;

    /** @var int Gets or sets a count columns in parameters panel. */
    public $parametersPanelColumnsCount = 2;

    /** @var int Gets or sets a minimum count of variables in parameters panel for multi-column display mode. */
    public $minParametersCountForMultiColumns = 5;

    /** @var string Gets or sets a date format for datetime parameters in parameters panel. The default is the client browser date format. */
    public $parametersPanelDateFormat = '';

    /** @var bool Gets or sets a value which indicates that variable items will be sorted. */
    public $parametersPanelSortDataItems = false;

    /** @var StiInterfaceType [enum] Gets or sets the type of the viewer interface. */
    public $interfaceType = StiInterfaceType::Auto;

    /** @var StiChartRenderType [enum] Gets or sets the type of the chart in the viewer. */
    public $chartRenderType = StiChartRenderType::AnimatedVector;

    /** @var StiHtmlExportMode [enum] Gets or sets a method how the viewer will show a report. */
    public $reportDisplayMode = StiHtmlExportMode::FromReport;

    /** @var StiFirstDayOfWeek [enum] Gets or sets the first day of week in the date picker */
    public $datePickerFirstDayOfWeek = StiFirstDayOfWeek::Auto;

    /** @var bool Gets or sets a value, which indicates that the current day will be included in the ranges of the date picker. */
    public $datePickerIncludeCurrentDayForRanges = false;

    /** @var bool Gets or sets a value which allows touch zoom in the viewer. */
    public $allowTouchZoom = true;

    /** @var bool Gets or sets a value which indicates that allows mobile mode of the viewer interface. */
    public $allowMobileMode = true;

    /** @var bool Gets or sets a value which indicates that if a report contains several pages, then they will be combined in preview. */
    public $combineReportPages = false;

    /** @var StiViewerTheme [enum] Gets or sets the current visual theme which is used for drawing visual elements of the viewer. */
    public $theme = StiViewerTheme::Office2022WhiteBlue;

    /** @var StiWebUIIconSet [enum] Gets or sets the current icon set for the viewer. */
    public $iconSet = StiWebUIIconSet::Auto;


### Helpers

    public function setComponent(StiComponent $component)
    {
        parent::setComponent($component);

        $this->id .= '.appearance';
    }
}