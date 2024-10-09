<?php

namespace Stimulsoft\Designer;

use Stimulsoft\StiComponent;
use Stimulsoft\StiComponentOptions;
use Stimulsoft\Viewer\StiViewerOptions;

class StiDesignerOptions extends StiComponentOptions
{

### Options

    /** @var string Gets or sets a path to the localization file for the designer. */
    public $localization;

    /** @var StiAppearanceOptions A class which controls settings of the designer appearance. */
    public $appearance;

    /** @var StiToolbarOptions A class which controls settings of the designer toolbar. */
    public $toolbar;

    /** @var StiBandsOptions A class which controls settings of the bands. */
    public $bands;

    /** @var StiCrossBandsOptions A class which controls settings of the cross-bands. */
    public $crossBands;

    /** @var StiReportComponentsOptions A class which controls settings of the components. */
    public $components;

    /** @var StiDashboardElementsOptions A class which controls settings of the dashboardElements. */
    public $dashboardElements;

    /** @var StiDictionaryOptions A class which controls settings of the dictionary. */
    public $dictionary;

    /** @var string Gets or sets the width of the designer. */
    public $width = '100%';

    /** @var string Gets or sets the height of the designer. */
    public $height = '800px';

    /** @var StiViewerOptions A class which controls settings of the preview window. */
    public $viewerOptions;

    private $localizations = [];


### Helpers

    /** Adds localization to the designer menu */
    public function addLocalization($path)
    {
        $this->localizations[] = $path;
    }

    public function getHtml(): string
    {
        $result = '';

        foreach ($this->localizations as $localization) {
            $localizationPath = $this->getLocalizationPath($localization);
            if ($localizationPath != null)
                $result .= "Stimulsoft.Base.Localization.StiLocalization.addLocalizationFile('$localizationPath', true);\n";
        }

        $localizationPath = $this->getLocalizationPath($this->localization);
        if ($localizationPath != null)
            $result .= "Stimulsoft.Base.Localization.StiLocalization.setLocalizationFile('$localizationPath');\n";

        $result .= "let $this->id = new Stimulsoft.Designer.StiDesignerOptions();\n";

        return $result . parent::getHtml();
    }


### Constructor

    public function __construct()
    {
        $this->appearance = new StiAppearanceOptions();
        $this->toolbar = new StiToolbarOptions();
        $this->bands = new StiBandsOptions();
        $this->crossBands = new StiCrossBandsOptions();
        $this->components = new StiReportComponentsOptions();
        $this->dashboardElements = new StiDashboardElementsOptions();
        $this->dictionary = new StiDictionaryOptions();
        $this->viewerOptions = new StiViewerOptions();
    }
}