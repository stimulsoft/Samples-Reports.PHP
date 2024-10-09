<?php

namespace Stimulsoft\Viewer;

use Stimulsoft\Enums\StiComponentType;
use Stimulsoft\StiComponent;
use Stimulsoft\StiComponentOptions;

/** A class which controls settings of the viewer. */
class StiViewerOptions extends StiComponentOptions
{

### Options

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


### Helpers

    private function isPreviewControl(): bool
    {
        return $this->component != null && $this->component->getComponentType() == StiComponentType::Designer;
    }

    public function setComponent(StiComponent $component)
    {
        parent::setComponent($component);

        if ($this->isPreviewControl()) {
            $this->id .= '.viewerOptions';

            $properties = $this->getProperties();
            foreach ($properties as $name) {
                if ($this->$name instanceof StiComponentOptions)
                    $this->$name->id = str_replace('.', '.viewerOptions.', $this->$name->id);
            }
        }
    }


### HTML

    public function getHtml(): string
    {
        if ($this->isPreviewControl())
            return parent::getHtml();

        $result = '';

        $localizationPath = $this->getLocalizationPath($this->localization);
        if ($localizationPath != null)
            $result .= "Stimulsoft.Base.Localization.StiLocalization.setLocalizationFile('$localizationPath');\n";

        $result .= "let $this->id = new Stimulsoft.Viewer.StiViewerOptions();\n";

        return $result . parent::getHtml();
    }


### Constructor

    public function __construct()
    {
        $this->appearance = new StiAppearanceOptions();
        $this->toolbar = new StiToolbarOptions();
        $this->exports = new StiExportsOptions();
        $this->email = new StiEmailOptions();
    }
}