<?php

namespace Stimulsoft\Designer;

use Stimulsoft\Designer\Enums\StiDesignerRibbonType;
use Stimulsoft\Designer\Enums\StiDesignerTheme;
use Stimulsoft\Designer\Enums\StiFirstDayOfWeek;
use Stimulsoft\Designer\Enums\StiInterfaceType;
use Stimulsoft\Designer\Enums\StiPropertiesGridPosition;
use Stimulsoft\Designer\Enums\StiReportUnitType;
use Stimulsoft\Designer\Enums\StiWebUIIconSet;
use Stimulsoft\Designer\Enums\StiWizardType;
use Stimulsoft\StiComponent;
use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the designer appearance. */
class StiAppearanceOptions extends StiComponentOptions
{

### Options

    /** @var StiReportUnitType [enum] Gets or sets a default value of unit in the designer. */
    public $defaultUnit = StiReportUnitType::Centimeters;

    /** @var StiInterfaceType [enum] Gets or sets the type of the designer interface. */
    public $interfaceType = StiInterfaceType::Auto;

    /** @var bool Gets or sets a value which indicates that animation is enabled. */
    public $showAnimation = true;

    /** @var bool Gets or sets a visibility of the save dialog of the designer. */
    public $showSaveDialog = true;

    /** @var bool Gets or sets a value which indicates that show or hide tooltips. */
    public $showTooltips = true;

    /** @var bool Gets or sets a value which indicates that show or hide tooltips help icon. */
    public $showTooltipsHelp = true;

    /** @var bool Gets or sets a value which indicates that show or hide the help button in dialogs. */
    public $showDialogsHelp = true;

    /** @var bool Gets or sets a value which indicates that the designer is displayed in full screen mode. */
    public $fullScreenMode = false;

    /** @var bool Gets or sets a value which indicates that the designer will be maximized after creation. */
    public $maximizeAfterCreating = false;

    /** @var bool Gets or sets a visibility of the localization control of the designer. */
    public $showLocalization = true;

    /** @var bool Allow the designer to change the window title. */
    public $allowChangeWindowTitle = true;

    /** @var bool Gets or sets a visibility of the properties grid in the designer. */
    public $showPropertiesGrid = true;

    /** @var bool Gets or sets a visibility of the report tree in the designer. */
    public $showReportTree = true;

    /** @var StiPropertiesGridPosition [enum] Gets or sets a position of the properties grid in the designer. */
    public $propertiesGridPosition = StiPropertiesGridPosition::Left;

    /** @var bool Gets or sets a visibility of the system fonts in the fonts list. */
    public $showSystemFonts = true;

    /** @var StiFirstDayOfWeek [enum] Gets or sets the first day of week in the date picker */
    public $datePickerFirstDayOfWeek = StiFirstDayOfWeek::Auto;

    /** @var int Gets or sets a maximum level of undo actions with the report. A large number of actions consume more memory on the server side. */
    public $undoMaxLevel = 6;

    /** @var StiWizardType [enum] Gets or sets a value of the wizard type which should be run after designer starts. */
    public $wizardTypeRunningAfterLoad = StiWizardType::None;

    /** @var bool Gets or sets a value which indicates that allows word wrap in the text editors. */
    public $allowWordWrapTextEditors = true;

    /** @var bool Allows loading custom fonts to the client side. */
    public $allowLoadingCustomFontsToClientSide = false;

    /** @var string Gets or sets a date format for date controls. */
    public $formatForDateControls = '';

    /** @var bool Gets or sets a value which enables or disables the short cut keys of the designer. */
    public $enableShortCutKeys = true;

    /** @var StiDesignerRibbonType [enum] Gets or sets a default value of the ribbon type in the designer. */
    public $defaultRibbonType = StiDesignerRibbonType::Classic;

    /** @var int Gets or sets the report showing zoom. The default value is 100. */
    public $zoom = 100;

    /** @var StiDesignerTheme [enum] Gets or sets the current visual theme which is used for drawing visual elements of the designer. */
    public $theme = StiDesignerTheme::Office2022WhiteBlue;

    /** @var StiWebUIIconSet [enum] Gets or sets the current icon set for the designer. */
    public $iconSet = StiWebUIIconSet::Auto;


### Helpers

    public function setComponent(StiComponent $component)
    {
        parent::setComponent($component);

        $this->id .= '.appearance';
    }
}