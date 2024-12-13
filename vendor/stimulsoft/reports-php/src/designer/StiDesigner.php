<?php

namespace Stimulsoft\Designer;

use Stimulsoft\Enums\StiComponentType;
use Stimulsoft\Enums\StiEventType;
use Stimulsoft\Enums\StiHtmlMode;
use Stimulsoft\Events\StiComponentEvent;
use Stimulsoft\Events\StiReportEventArgs;
use Stimulsoft\Report\StiReport;
use Stimulsoft\StiComponent;
use Stimulsoft\StiHandler;

class StiDesigner extends StiComponent
{

### Events

    /** @var StiComponentEvent The event is invoked after creation a new report in the designer. PHP and JavaScript functions are supported. */
    public $onCreateReport;

    /**
     * @var StiComponentEvent
     * The event is invoked before opening a report from the designer menu after clicking the button. Only JavaScript functions are supported.
     */
    public $onOpenReport;

    /** @var StiComponentEvent The event is invoked after opening a report before sending to the designer. PHP and JavaScript functions are supported. */
    public $onOpenedReport;

    /** @var StiComponentEvent The event is invoked when saving a report in the designer. PHP and JavaScript functions are supported. */
    public $onSaveReport;

    /**
     * @var StiComponentEvent
     * The event is invoked when saving a report in the designer with a preliminary input of the file name.
     * PHP and JavaScript functions are supported.
     */
    public $onSaveAsReport;

    /** @var StiComponentEvent The event is invoked when going to the report preview tab. PHP and JavaScript functions are supported. */
    public $onPreviewReport;

    /**
     * @var StiComponentEvent
     * The event is invoked after the report is closed, before the report is unassigned from the designer. PHP and JavaScript functions are supported.
     */
    public $onCloseReport;

    /**
     * @var StiComponentEvent
     * The event is invoked when by clicking the Exit button in the main menu of the designer. Only JavaScript functions are supported.
     */
    public $onExit;


### Properties

    /** @var StiReport Gets or sets a report object for the viewer. */
    public $report;

    /** @var StiDesignerOptions All viewer component options, divided by categories. */
    public $options;


### Event handlers

    private function getCreateReportResult()
    {
        $args = new StiReportEventArgs($this->handler->request);
        $result = $this->getDefaultEventResult($this->onCreateReport, $args);
        if ($result != null && $args->report != $this->handler->request->report)
            $result->report = $args->report;

        return $result;
    }

    private function getOpenedReportResult()
    {
        $args = new StiReportEventArgs($this->handler->request);
        return $this->getDefaultEventResult($this->onOpenedReport, $args);
    }

    private function getSaveReportResult()
    {
        $args = new StiReportEventArgs($this->handler->request);
        return $this->getDefaultEventResult($this->onSaveReport, $args);
    }

    private function getSaveAsReportResult()
    {
        $args = new StiReportEventArgs($this->handler->request);
        return $this->getDefaultEventResult($this->onSaveAsReport, $args);
    }

    private function getPreviewReportResult()
    {
        $args = new StiReportEventArgs($this->handler->request);
        $result = $this->getDefaultEventResult($this->onPreviewReport, $args);
        if ($result != null && $args->report != $this->handler->request->report)
            $result->report = $args->report;

        return $result;
    }

    private function getCloseReportResult()
    {
        $args = new StiReportEventArgs($this->handler->request);
        return $this->getDefaultEventResult($this->onCloseReport, $args);
    }

    public function getEventResult()
    {
        $this->updateEvents();
        $request = $this->getRequest();

        if ($request->event == StiEventType::CreateReport)
            return $this->getCreateReportResult();

        if ($request->event == StiEventType::OpenedReport)
            return $this->getOpenedReportResult();

        if ($request->event == StiEventType::SaveReport)
            return $this->getSaveReportResult();

        if ($request->event == StiEventType::SaveAsReport)
            return $this->getSaveAsReportResult();

        if ($request->event == StiEventType::PreviewReport)
            return $this->getPreviewReportResult();

        if ($request->event == StiEventType::CloseReport)
            return $this->getCloseReportResult();

        return parent::getEventResult();
    }


### Helpers

    protected function updateObjects()
    {
        parent::updateObjects();

        $this->setOptions($this->options);
        $this->setHandler($this->handler);
        $this->setReport($this->report);
    }

    protected function updateEvents()
    {
        parent::updateEvents();

        $this->updateEvent('onCreateReport');
        $this->updateEvent('onOpenReport');
        $this->updateEvent('onOpenedReport');
        $this->updateEvent('onSaveReport');
        $this->updateEvent('onSaveAsReport');
        $this->updateEvent('onPreviewReport');
        $this->updateEvent('onCloseReport');
        $this->updateEvent('onExit');
    }

    public function getComponentType()
    {
        return StiComponentType::Designer;
    }

    public function setOptions(StiDesignerOptions $options)
    {
        $this->options = $options;
        $options->setComponent($this);
    }

    public function setHandler(StiHandler $handler)
    {
        parent::setHandler($handler);

        if ($handler != null) {
            $handler->onCreateReport = $this->onCreateReport;
            $handler->onOpenReport = $this->onOpenReport;
            $handler->onOpenedReport = $this->onOpenedReport;
            $handler->onSaveReport = $this->onSaveReport;
            $handler->onSaveAsReport = $this->onSaveAsReport;
            $handler->onPreviewReport = $this->onPreviewReport;
            $handler->onCloseReport = $this->onCloseReport;
            $handler->onExit = $this->onExit;
        }
    }

    /**
     * @param StiReport|null $report Prepared report object.
     */
    public function setReport($report)
    {
        $this->report = $report;

        if ($report != null) {
            $this->updateEvents();
            $report->onDatabaseConnect = $this->onDatabaseConnect;
            $report->onBeginProcessData = $this->onBeginProcessData;
            $report->onEndProcessData = $this->onEndProcessData;
            $report->onPrepareVariables = $this->onPrepareVariables;

            $report->setHandler($this->handler);
            $report->license = $this->license;
            $report->fontCollection = $this->fontCollection;
            $report->functions = $this->functions;
        }
    }


### HTML

    protected function getComponentHtml(): string
    {
        $result = parent::getComponentHtml();

        $result .= $this->options->getHtml();
        $result .= "let $this->id = new Stimulsoft.Designer.StiDesigner({$this->options->id}, '$this->id', false);\n";

        $result .= $this->onPrepareVariables->getHtml(true);
        $result .= $this->onBeginProcessData->getHtml(true);
        $result .= $this->onEndProcessData->getHtml();
        $result .= $this->onCreateReport->getHtml(true);
        $result .= $this->onOpenReport->getHtml();
        $result .= $this->onOpenedReport->getHtml();
        $result .= $this->onSaveReport->getHtml(false, true);
        $result .= $this->onSaveAsReport->getHtml(false, true);
        $result .= $this->onPreviewReport->getHtml(true);
        $result .= $this->onCloseReport->getHtml(true);
        $result .= $this->onExit->getHtml(false, false, false);

        if ($this->report != null) {
            if (!$this->report->htmlRendered)
                $result .= $this->report->getHtml(StiHtmlMode::Scripts);

            $result .= "$this->id.report = {$this->report->id};\n";
        }

        $result .= "$this->id.renderHtml('{$this->id}Content');\n";

        return $result;
    }

    public function getHtml($mode = StiHtmlMode::HtmlScripts): string
    {
        if ($mode == StiHtmlMode::HtmlPage)
            $this->options->appearance->fullScreenMode = true;

        return parent::getHtml($mode);
    }


### Constructor

    public function __construct($id = 'designer', StiDesignerOptions $options = null)
    {
        parent::__construct();

        if (StiHandler::$legacyMode && $id instanceof StiDesignerOptions) {
            $options = $id;
            $id = 'designer';
        }

        $this->id = strlen($id ?? '') > 0 ? $id : 'designer';
        $this->options = $options ?? new StiDesignerOptions();
        $this->setOptions($this->options);
        $this->setHandler($this->handler);
    }
}