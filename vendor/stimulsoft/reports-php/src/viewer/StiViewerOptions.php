<?php

namespace Stimulsoft\Viewer;

use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the viewer. */
class StiViewerOptions extends StiComponentOptions
{
    /** @var string Gets or sets a path to the localization file for the viewer. */
    public $localization;

    /** @var StiAppearanceOptions A class which controls settings of the viewer appearance. */
    public $appearance;

    /** @var StiToolbarOptions A class which controls settings of the viewer toolbar. */
    public $toolbar;

    /** @var StiExportsOptions A class which controls the export options. */
    public $exports;

    /** @var StiEmailOptions A class which controls the export options. */
    public $email;

    /** @var string Gets or sets the width of the viewer. */
    public $width = '100%';

    /** @var string Gets or sets the height of the viewer. */
    public $height = '';

    /** Get the HTML representation of the component. */
    public function getHtml()
    {
        if (strpos($this->property, '.') > 0)
            return parent::getHtml();

        $result = '';

        $localizationPath = $this->getLocalizationPath($this->localization);
        if ($localizationPath != null)
            $result .= "Stimulsoft.Base.Localization.StiLocalization.setLocalizationFile('$localizationPath');\n";

        $result .= "let $this->property = new Stimulsoft.Viewer.StiViewerOptions();\n";

        return $result . parent::getHtml();
    }

    public function __construct($property = 'viewerOptions')
    {
        parent::__construct($property);

        $this->appearance = new StiAppearanceOptions("$property.appearance");
        $this->toolbar = new StiToolbarOptions("$property.toolbar");
        $this->exports = new StiExportsOptions("$property.exports");
        $this->email = new StiEmailOptions("$property.email");
    }
}