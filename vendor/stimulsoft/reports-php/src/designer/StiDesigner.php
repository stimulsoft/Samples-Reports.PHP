<?php

namespace Stimulsoft\Designer;

use Stimulsoft\Report\StiReport;
use Stimulsoft\StiHtmlComponent;

class StiDesigner extends StiHtmlComponent
{
    /** @var StiDesignerOptions */
    public $options;

    /** @var StiReport */
    public $report;

    /** The event is invoked before rendering a report after preparing report variables. */
    public $onPrepareVariables = false;

    /** The event is invoked before data request, which are needed to render a report. */
    public $onBeginProcessData = false;

    /** The event is invoked after loading data before rendering a report. */
    public $onEndProcessData;

    /** The event is invoked after creation a new report in the designer. */
    public $onCreateReport = false;

    /** The event is invoked before opening a report from the designer menu. */
    public $onOpenReport = false;

    /** The event is invoked when saving a report in the designer. */
    public $onSaveReport = false;

    /** The event is invoked when saving a report in the designer with a preliminary input of the file name. */
    public $onSaveAsReport = false;

    /** The event is invoked when going to the report view tab. */
    public $onPreviewReport = false;

    /** The event is invoked when by clicking the Exit button in the main menu of the designer */
    public $onExit = false;

    /** Get the HTML representation of the component. */
    public function getHtml($element = null)
    {
        $result = '';

        if ($this->options instanceof StiDesignerOptions && !$this->options->isHtmlRendered)
            $result .= $this->options->getHtml();

        $optionsProperty = $this->options instanceof StiDesignerOptions ? $this->options->property : 'null';
        $designerProperty = $this->id == 'StiDesigner' ? 'designer' : $this->id;
        $result .= "let $designerProperty = new Stimulsoft.Designer.StiDesigner($optionsProperty, '$this->id', false);\n";

        if ($this->onPrepareVariables)
            $result .= $this->getEventHtml('onPrepareVariables', true);

        if ($this->onBeginProcessData)
            $result .= $this->getEventHtml('onBeginProcessData', true);

        if ($this->onEndProcessData)
            $result .= $this->getEventHtml('onEndProcessData');

        if ($this->onCreateReport)
            $result .= $this->getEventHtml('onCreateReport', true);

        if ($this->onOpenReport)
            $result .= $this->getEventHtml('onOpenReport');

        if ($this->onSaveReport)
            $result .= $this->getEventHtml('onSaveReport', true);

        if ($this->onSaveAsReport)
            $result .= $this->getEventHtml('onSaveAsReport', true);

        if ($this->onPreviewReport)
            $result .= $this->getEventHtml('onPreviewReport');

        if ($this->onExit)
            $result .= $this->getEventHtml('onExit');

        if ($this->report instanceof StiReport) {
            if (!$this->report->isHtmlRendered)
                $result .= $this->report->getHtml();

            $result .= "$designerProperty.report = {$this->report->id};\n";
        }

        $result .= "$designerProperty.renderHtml(" . (strlen($element) > 0 ? "'$element'" : '') . ");\n";

        return $result;
    }

    /** Output of the HTML representation of the component. */
    public function renderHtml($element = null)
    {
        echo $this->getHtml($element);
    }

    public function __construct($options = null, $id = 'StiDesigner')
    {
        $this->options = $options;
        $this->id = strlen($id) > 0 ? $id : 'StiDesigner';
    }
}