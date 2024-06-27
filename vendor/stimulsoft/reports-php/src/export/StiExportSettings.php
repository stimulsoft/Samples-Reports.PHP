<?php

namespace Stimulsoft\Export;

use ReflectionClass;
use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\Report\StiPagesRange;
use Stimulsoft\StiJsElement;

class StiExportSettings extends StiJsElement
{

### Properties

    public $pageRange = null;


### Helpers

    public function setPageRange(StiPagesRange $pageRange = null)
    {
        if ($pageRange == null)
            $pageRange = new StiPagesRange();

        $pageRange->id = "$this->id.pageRange";
        $this->pageRange = $pageRange;
    }

    protected function getStringValue(string $name, $value)
    {
        if (is_string($value) && $name == 'encoding')
            return "eval(\"$value\")";

        return parent::getStringValue($name, $value);
    }

    public function getExportFormat(): int
    {
        return StiExportFormat::None;
    }


### HTML

    public function getHtml(): string
    {
        $reflection = new ReflectionClass($this);
        $class = $reflection->getName();
        $className = $reflection->getShortName();
        $result = "let $this->id = new Stimulsoft.Report.Export.$className();\n";

        $default = new $class();
        $properties = $this->getProperties();
        foreach ($properties as $name) {
            if ($default->$name != $this->$name) {
                if ($this->$name instanceof StiPagesRange)
                    $result .= $this->$name->getHtml(false);
                else {
                    $jsvalue = $this->getStringValue($name, $this->$name);
                    $result .= "$this->id.$name = $jsvalue;\n";
                }
            }
        }

        return $result . parent::getHtml();
    }


### Constructor

    public function __construct()
    {
        $this->id = 'settings';
        $this->setPageRange();
    }
}