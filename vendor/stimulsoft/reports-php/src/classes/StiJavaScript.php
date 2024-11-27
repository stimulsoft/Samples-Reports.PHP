<?php

namespace Stimulsoft;

use Stimulsoft\Enums\StiComponentType;

class StiJavaScript extends StiElement
{

### Fields

    /** @var StiComponent */
    private $component;
    private $componentType;
    private $head = [];


### Options

    public $reportsChart = true;
    public $reportsExport = true;
    public $reportsImportXlsx = true;
    public $reportsMaps = true;
    public $blocklyEditor = true;

    public $usePacked = false;
    public $useRelativeUrls = true;
    public $relativePath = '';
    public $useStaticUrls = true;

    /**
     * @deprecated Please use the same properties in the main class. For this property to work, you need to call StiHandler::enableLegacyMode();
     */
    public $options = null;


### Helpers

    public function setComponent(StiComponent $component)
    {
        $this->component = $component;
        $this->componentType = $component->getComponentType();
    }

    public function getUrl(): string
    {
        return $this->component != null && $this->component->handler != null ? $this->component->handler->getUrl() : '';
    }

    public function getRootUrl(): string
    {
        return $this->useRelativeUrls ? $this->relativePath : '/';
    }

    private function updateOptions()
    {
        if (StiHandler::$legacyMode)
            StiFunctions::populateObject($this, $this->options);
    }

    public function appendHead(string $value)
    {
        $this->head[] = $value;
    }


### HTML

    /**
     * Gets the HTML representation of the component.
     */
    public function getHtml(): string
    {
        $this->updateOptions();

        $result = '';
        foreach ($this->head as $name) {
            $result .= "$name\n";
        }

        $extension = $this->usePacked ? 'pack.js' : 'js';
        $reportsSet = $this->reportsChart && $this->reportsExport && $this->reportsImportXlsx && $this->reportsMaps && $this->blocklyEditor;

        $scripts = [];
        if ($reportsSet)
            $scripts[] = "stimulsoft.reports.$extension";
        else {
            $scripts[] = "stimulsoft.reports.engine.$extension";
            if ($this->reportsChart)
                $scripts[] = "stimulsoft.reports.chart.$extension";
            if ($this->reportsExport)
                $scripts[] = "stimulsoft.reports.export.$extension";
            if ($this->reportsMaps)
                $scripts[] = "stimulsoft.reports.maps.$extension";
            if ($this->reportsImportXlsx)
                $scripts[] = "stimulsoft.reports.import.xlsx.$extension";
        }

        if (StiFunctions::isDashboardsProduct())
            $scripts[] = "stimulsoft.dashboards.$extension";

        if ($this->componentType == StiComponentType::Viewer || $this->componentType == StiComponentType::Designer)
            $scripts[] = "stimulsoft.viewer.$extension";

        if ($this->componentType == StiComponentType::Designer) {
            $scripts[] = "stimulsoft.designer.$extension";

            if ($this->blocklyEditor)
                $scripts[] = "stimulsoft.blockly.editor.$extension";
        }

        foreach ($scripts as $name) {
            $scriptName = str_replace('.', '_', $name);
            $rendered = array_key_exists("Stimulsoft_Scripts_$scriptName", $GLOBALS) && $GLOBALS["Stimulsoft_Scripts_$scriptName"];
            if (!$rendered) {
                $product = strpos($name, 'dashboards') > 0 ? 'dashboards-php' : 'reports-php';
                $root = $this->getRootUrl();
                $url = $this->getUrl();
                $url .= strpos($url, '?') === false ? '?' : '&';
                $result .= $this->useStaticUrls && $name != 'stimulsoft.handler.js'
                    ? "<script src=\"{$root}vendor/stimulsoft/$product/scripts/$name\"></script>\n"
                    : "<script src=\"{$url}sti_event=GetResource&sti_data=$name\"></script>\n";
                $GLOBALS["Stimulsoft_Scripts_$scriptName"] = true;
            }
        }

        $scriptName = 'stimulsoft_handler_js';
        $rendered = array_key_exists("Stimulsoft_Scripts_$scriptName", $GLOBALS) && $GLOBALS["Stimulsoft_Scripts_$scriptName"];
        if (!$rendered && $this->component != null && $this->component->handler != null) {
            $result .= $this->component->handler->getHtml();
            $GLOBALS["Stimulsoft_Scripts_$scriptName"] = true;
        }

        return $result . parent::getHtml();
    }


### Constructor

    /**
     * StiJavaScript constructor.
     * @param StiComponent|StiComponentType|string $component
     */
    public function __construct($component)
    {
        if (StiHandler::$legacyMode)
            $this->options = new \stdClass();

        if ($component instanceof StiComponent)
            $this->setComponent($component);
        else
            $this->componentType = $component;
    }
}