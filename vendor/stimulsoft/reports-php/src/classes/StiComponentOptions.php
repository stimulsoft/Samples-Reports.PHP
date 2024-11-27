<?php

namespace Stimulsoft;

class StiComponentOptions extends StiJsElement
{

### Properties

    /** @var StiComponent */
    public $component;


### Helpers

    protected function getIgnoredProperties(): array
    {
        return array_merge(parent::getIgnoredProperties(), ['component', 'localization']);
    }

    protected function getStringValue(string $name, $value)
    {
        if (substr_compare($name, 'Color', -5) === 0)
            return $this->getColorValue($value);

        return parent::getStringValue($name, $value);
    }

    private function getColorValue($value): string
    {
        if ($value == null || strlen($value) == 0)
            return 'Stimulsoft.System.Drawing.Color.transparent';

        if ($value[0] == '#') {
            list($r, $g, $b) = sscanf($value, '#%02x%02x%02x');
            return "Stimulsoft.System.Drawing.Color.fromArgb(255, $r, $g, $b)";
        }

        return "Stimulsoft.System.Drawing.Color.$value";
    }

    protected function getLocalizationPath($localization)
    {
        if (is_null($localization) || strlen($localization) == 0)
            return null;

        if (strlen($localization) < 5 || substr($localization, -4) != '.xml')
            $localization .= '.xml';

        if (!preg_match('/[\/\\\]/', $localization)) {
            $root = $this->component->javascript->getRootUrl();
            $localization = "{$root}vendor/stimulsoft/reports-php/localization/$localization";
        }

        return $localization;
    }

    /**
     * Sets the component to which these options apply.
     */
    public function setComponent(StiComponent $component)
    {
        $this->component = $component;
        $this->id = $component->id . 'Options';

        $properties = $this->getProperties();
        foreach ($properties as $name) {
            if ($this->$name instanceof StiComponentOptions)
                $this->$name->setComponent($component);
        }
    }


### HTML

    public function getHtml(): string
    {
        $properties = $this->getProperties();
        $default = get_class_vars(get_class($this));
        $result = '';
        foreach ($properties as $name) {
            $value = $this->$name;
            if ($value instanceof StiComponentOptions)
                $result .= $value->getHtml();
            else if ($value != $default[$name]) {
                $stringValue = $this->getStringValue($name, $value);
                $result .= "$this->id.$name = $stringValue;\n";
            }
        }

        return $result . parent::getHtml();
    }
}