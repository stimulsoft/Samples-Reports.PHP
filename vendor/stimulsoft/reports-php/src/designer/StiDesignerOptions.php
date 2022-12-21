<?php

namespace Stimulsoft\Designer;

use Stimulsoft\StiComponentOptions;
use Stimulsoft\Viewer\StiViewerOptions;

class StiDesignerOptions extends StiComponentOptions
{
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

    /** @var StiComponentOptions A class which controls settings of the components. */
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

    /** Get the HTML representation of the component. */
    public function getHtml()
    {
        $result = '';

        $localizationPath = $this->getLocalizationPath($this->localization);
        if ($localizationPath != null)
            $result .= "Stimulsoft.Base.Localization.StiLocalization.setLocalizationFile('$localizationPath');\n";

        $result .= "let $this->property = new Stimulsoft.Designer.StiDesignerOptions();\n";

        return $result . parent::getHtml();
    }

    public function __construct($property = 'designerOptions')
    {
        parent::__construct($property);

        $this->appearance = new StiAppearanceOptions("$property.appearance");
        $this->toolbar = new StiToolbarOptions("$property.toolbar");
        $this->bands = new StiBandsOptions("$property.bands");
        $this->crossBands = new StiCrossBandsOptions("$property.crossBands");
        $this->components = new StiComponentOptions("$property.components");
        $this->dashboardElements = new StiDashboardElementsOptions("$property.dashboardElements");
        $this->dictionary = new StiDictionaryOptions("$property.dictionary");
        $this->viewerOptions = new StiViewerOptions("$property.viewerOptions");
    }
}