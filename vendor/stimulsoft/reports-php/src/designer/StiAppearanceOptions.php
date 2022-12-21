<?php

namespace Stimulsoft\Designer;

use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the designer appearance. */
class StiAppearanceOptions extends StiComponentOptions
{
    protected $enums = [
        'defaultUnit', 'interfaceType', 'propertiesGridPosition', 'datePickerFirstDayOfWeek',
        'wizardTypeRunningAfterLoad', 'zoom', 'theme'
    ];

    /** Gets or sets a default value of unit in the designer. */
    public $defaultUnit = StiReportUnitType::Centimeters;

    /** Gets or sets the type of the designer interface. */
    public $interfaceType = StiInterfaceType::Auto;

    /** Gets or sets a value which indicates that animation is enabled. */
    public $showAnimation = true;

    /** Gets or sets a visibility of the save dialog of the designer. */
    public $showSaveDialog = true;

    /** Gets or sets a value which indicates that show or hide tooltips. */
    public $showTooltips = true;

    /** Gets or sets a value which indicates that show or hide tooltips help icon. */
    public $showTooltipsHelp = true;

    /** Gets or sets a value which indicates that show or hide the help button in dialogs. */
    public $showDialogsHelp = true;

    /** Gets or sets a value which indicates that the designer is displayed in full screen mode. */
    public $fullScreenMode = false;

    /** Gets or sets a value which indicates that the designer will be maximized after creation. */
    public $maximizeAfterCreating = false;

    /** Gets or sets a visibility of the localization control of the designer. */
    public $showLocalization = true;

    /** Allow the designer to change the window title. */
    public $allowChangeWindowTitle = true;

    /** Gets or sets a visibility of the properties grid in the designer. */
    public $showPropertiesGrid = true;

    /** Gets or sets a visibility of the report tree in the designer. */
    public $showReportTree = true;

    /** Gets or sets a position of the properties grid in the designer. */
    public $propertiesGridPosition = StiPropertiesGridPosition::Left;

    /** Gets or sets a visibility of the system fonts in the fonts list. */
    public $showSystemFonts = true;

    /** Gets or sets the first day of week in the date picker */
    public $datePickerFirstDayOfWeek = StiFirstDayOfWeek::Auto;

    /** Gets or sets a maximum level of undo actions with the report. A large number of actions consume more memory on the server side. */
    public $undoMaxLevel = 6;

    /** Gets or sets a value of the wizard type which should be run after designer starts. */
    public $wizardTypeRunningAfterLoad = StiWizardType::None;

    /** Gets or sets a value which indicates that allows word wrap in the text editors. */
    public $allowWordWrapTextEditors = true;

    /** Gets or sets the report showing zoom. The default value is 100. */
    public $zoom = 100;

    /** Gets or sets the current visual theme which is used for drawing visual elements of the designer. */
    public $theme = StiDesignerTheme::Office2022WhiteBlue;
}