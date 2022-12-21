<?php

namespace Stimulsoft;

class StiJavaScript
{
    public $componentType;
    public $options;
    public $usePacked = false;
    public $useRelativeUrls = true;

    public function getHtml()
    {
        $dashboards = class_exists('\Stimulsoft\Report\StiDashboard');
        $extension = $this->usePacked ? 'pack.js' : 'js';

        $scripts = array();
        if ($this->options->reports)
            $scripts[] = "stimulsoft.reports.$extension";
        else {
            if ($this->options->reportsChart)
                $scripts[] = "stimulsoft.reports.chart.$extension";
            if ($this->options->reportsExport)
                $scripts[] = "stimulsoft.reports.export.$extension";
            if ($this->options->reportsMaps)
                $scripts[] = "stimulsoft.reports.maps.$extension";
            if ($this->options->reportsImportXlsx)
                $scripts[] = "stimulsoft.reports.import.xlsx.$extension";
        }

        if ($dashboards)
            $scripts[] = "stimulsoft.dashboards.$extension";

        if ($this->componentType == StiComponentType::Viewer || $this->componentType == StiComponentType::Designer)
            $scripts[] = "stimulsoft.viewer.$extension";

        if ($this->componentType == StiComponentType::Designer) {
            $scripts[] = "stimulsoft.designer.$extension";

            if ($this->options->blocklyEditor)
                $scripts[] = "stimulsoft.blockly.editor.$extension";
        }

        $result = '';
        foreach ($scripts as $name) {
            $product = strpos($name, 'dashboards') > 0 ? 'dashboards-php' : 'reports-php';
            $root = $this->useRelativeUrls ? '' : '/';
            $result .= "<script src=\"{$root}vendor/stimulsoft/$product/scripts/$name\" type=\"text/javascript\"></script>\n";
        }

        return $result;
    }

    public function renderHtml()
    {
        echo $this->getHtml();
    }

    public function __construct($componentType, $options = null)
    {
        $this->componentType = $componentType;
        $this->options = $options != null ? $options : new StiJavaScriptOptions();
    }
}