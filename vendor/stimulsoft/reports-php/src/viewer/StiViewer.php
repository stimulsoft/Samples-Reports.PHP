<?php

namespace Stimulsoft\Viewer;

use Stimulsoft\Report\StiReport;
use Stimulsoft\StiHtmlComponent;

class StiViewer extends StiHtmlComponent
{
    /** @var StiViewerOptions */
    public $options;

    /** @var StiReport */
    public $report;

    /** The event is invoked before rendering a report after preparing report variables. */
    public $onPrepareVariables;

    /** The event is invoked before data request, which needed to render a report. */
    public $onBeginProcessData;

    /** The event is invoked after loading data before rendering a report. */
    public $onEndProcessData;

    /** The event is invoked before printing a report. */
    public $onPrintReport;

    /** The event is invoked before exporting a report after the dialog of export settings. */
    public $onBeginExportReport;

    /** The event is invoked after exporting a report till its saving as a file. */
    public $onEndExportReport;

    /**
     * The event is invoked while interactive action of the viewer (dynamic sorting, collapsing, drill-down, applying of parameters)
     * until processing values by the report generator.
     */
    public $onInteraction;

    /** The event is invoked after exporting a report before sending it by Email. */
    public $onEmailReport;

    /** The event occurs when clicking on the Design button in the viewer toolbar. */
    public $onDesignReport;

    /** Get the HTML representation of the component. */
    public function getHtml($element = null)
    {
        $result = '';

        if ($this->options instanceof StiViewerOptions && !$this->options->isHtmlRendered)
            $result .= $this->options->getHtml();

        $optionsProperty = $this->options instanceof StiViewerOptions ? $this->options->property : 'null';
        $viewerProperty = $this->id == 'StiViewer' ? 'viewer' : $this->id;
        $result .= "let $viewerProperty = new Stimulsoft.Viewer.StiViewer($optionsProperty, '$this->id', false);\n";

        if ($this->onPrepareVariables)
            $result .= $this->getEventHtml('onPrepareVariables', true);

        if ($this->onBeginProcessData)
            $result .= $this->getEventHtml('onBeginProcessData', true);

        if ($this->onEndProcessData)
            $result .= $this->getEventHtml('onEndProcessData');

        if ($this->onPrintReport)
            $result .= $this->getEventHtml('onPrintReport');

        if ($this->onBeginExportReport)
            $result .= $this->getEventHtml('onBeginExportReport', true);

        if ($this->onEndExportReport)
            $result .= $this->getEventHtml('onEndExportReport', true, true);

        if ($this->onInteraction)
            $result .= $this->getEventHtml('onInteraction');

        if ($this->onEmailReport)
            $result .= $this->getEventHtml('onEmailReport');

        if ($this->onDesignReport)
            $result .= $this->getEventHtml('onDesignReport');

        if ($this->report instanceof StiReport) {
            if (!$this->report->isHtmlRendered)
                $result .= $this->report->getHtml();

            $result .= "$viewerProperty.report = {$this->report->id};\n";
        }

        $result .= "$viewerProperty.renderHtml(" . (strlen($element) > 0 ? "'$element'" : '') . ");\n";

        return $result;
    }

    /** Output of the HTML representation of the component. */
    public function renderHtml($element = null)
    {
        echo $this->getHtml($element);
    }

    public function __construct($options = null, $id = 'StiViewer')
    {
        $this->options = $options;
        $this->id = strlen($id) > 0 ? $id : 'StiViewer';
    }
}