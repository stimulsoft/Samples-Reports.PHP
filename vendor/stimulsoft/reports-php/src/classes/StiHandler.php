<?php

namespace Stimulsoft;

use Stimulsoft\Designer\StiDesigner;
use Stimulsoft\Enums\StiDatabaseType;
use Stimulsoft\Enums\StiEventType;
use Stimulsoft\Enums\StiHtmlMode;
use Stimulsoft\Events\StiComponentEvent;
use Stimulsoft\Events\StiVariablesEventArgs;
use Stimulsoft\Report\Enums\StiVariableType;
use Stimulsoft\Report\StiReport;
use Stimulsoft\Viewer\StiViewer;

/**
 * Event handler for all requests from components. Processes the incoming request, communicates with data adapters,
 * prepares parameters and triggers events, and performs all necessary actions. After this, the event handler
 * prepares a response for the web server.
 */
class StiHandler extends StiBaseHandler
{

### Properties

    public static $legacyMode = false;

    /** @var StiComponent */
    public $component;

    /** @var StiRequest */
    public $request;

    /** @var bool */
    public $htmlRendered = false;

    /**
     * @deprecated Please use the same properties in the main class. For this property to work, you need to call StiHandler::enableLegacyMode();
     */
    public $options = null;

    /**
     * @var StiLicense
     * @deprecated StiHandler no longer has a license object, please use the static class StiLicense.
     * For this property to work, you need to call StiHandler::enableLegacyMode();
     */
    public $license;

    /** @var int Timeout for waiting for a response from the server side, in seconds. */
    public $timeout = 30;

    /** @var bool Enable encryption of data transferred between the client and server. */
    public $encryptData = true;

    /** @var bool Enables automatic escaping of parameters in SQL queries. */
    public $escapeQueryParameters = true;

    /** @var bool Enables automatic passing of GET parameters from the current URL to the report as variables. */
    public $passQueryParametersToReport = false;

    /** @var bool Enables server-side file name checking for saving the report to eliminate dangerous values. */
    public $checkFileNames = true;


### Events: Component

    /** @var StiComponentEvent The event is invoked before connecting to the database after all parameters have been received. Only PHP functions are supported. */
    public $onDatabaseConnect;

    /** @var StiComponentEvent The event is invoked before rendering a report after preparing report variables. PHP and JavaScript functions are supported. */
    public $onPrepareVariables;

    /** @var StiComponentEvent The event is invoked before data request, which needed to render a report. PHP and JavaScript functions are supported. */
    public $onBeginProcessData;

    /** @var StiComponentEvent The event is invoked after loading data before rendering a report. PHP and JavaScript functions are supported. */
    public $onEndProcessData;


### Events: Report

    /** @var StiComponentEvent The event is invoked called before all actions related to report rendering. Only JavaScript functions are supported. */
    public $onBeforeRender;

    /** @var StiComponentEvent The event is invoked called immediately after report rendering. Only JavaScript functions are supported. */
    public $onAfterRender;


### Events: Viewer | Designer

    /**
     * @var StiComponentEvent
     * The event is invoked before opening a report from the viewer toolbar or from the designer menu after clicking the button.
     * Only JavaScript functions are supported.
     */
    public $onOpenReport;

    /**
     * @var StiComponentEvent The event is invoked after opening a report before showing in the viewer or before sending to the designer.
     * PHP and JavaScript functions are supported.
     */
    public $onOpenedReport;


### Events: Viewer

    /** @var StiComponentEvent The event is invoked before printing a report from the viewer. PHP and JavaScript functions are supported. */
    public $onPrintReport;

    /**
     * @var StiComponentEvent
     * The event is invoked before exporting a report from the viewer after the dialog of export settings.
     * PHP and JavaScript functions are supported.
     */
    public $onBeginExportReport;

    /** @var StiComponentEvent
     * The event is invoked after exporting a report from the viewer till its saving as a file.
     * PHP and JavaScript functions are supported.
     */
    public $onEndExportReport;

    /**
     * @var StiComponentEvent
     * The event is invoked while interactive action of the viewer (dynamic sorting, collapsing, drill-down, applying of parameters)
     * until processing values by the report generator. Only JavaScript functions are supported.
     */
    public $onInteraction;

    /** @var StiComponentEvent The event is invoked after exporting a report before sending it by Email. PHP and JavaScript functions are supported. */
    public $onEmailReport;

    /** @var StiComponentEvent The event occurs when clicking on the Design button in the viewer toolbar. Only JavaScript functions are supported. */
    public $onDesignReport;


### Events: Designer

    /** @var StiComponentEvent The event is invoked after creation a new report in the designer. PHP and JavaScript functions are supported. */
    public $onCreateReport;

    /** @var StiComponentEvent The event is invoked when saving a report in the designer. PHP and JavaScript functions are supported. */
    public $onSaveReport;

    /**
     * @var StiComponentEvent
     * The event is invoked when saving a report in the designer with a preliminary input of the file name.
     * PHP and JavaScript functions are supported.
     */
    public $onSaveAsReport;

    /** @var StiComponentEvent The event is invoked when going to the report preview tab in the designer. PHP and JavaScript functions are supported. */
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


### Legacy

    public static function enableLegacyMode()
    {
        StiBaseHandler::enableLegacyMode();

        StiHandler::$legacyMode = true;

        /** @deprecated Please use the 'Stimulsoft\Enums\StiComponentType' class. */
        class_alias('Stimulsoft\Enums\StiComponentType', 'Stimulsoft\StiComponentType');
        /** @deprecated Please use the 'Stimulsoft\Enums\StiEventType' class. */
        class_alias('Stimulsoft\Enums\StiEventType', 'Stimulsoft\StiEventType');
        /** @deprecated Please use the 'Stimulsoft\Export\Enums\StiExportFormat' class. */
        class_alias('Stimulsoft\Export\Enums\StiExportFormat', 'Stimulsoft\StiExportFormat');
        /** @deprecated Please use the 'Stimulsoft\Viewer\Enums\StiExportAction' class. */
        class_alias('Stimulsoft\Viewer\Enums\StiExportAction', 'Stimulsoft\StiExportAction');
        /** @deprecated Please use the 'Stimulsoft\Viewer\Enums\StiPrintAction' class. */
        class_alias('Stimulsoft\Viewer\Enums\StiPrintAction', 'Stimulsoft\StiPrintAction');

        /** @deprecated Please use the 'Stimulsoft\Report\Enums\StiRangeType' class. */
        class_alias('Stimulsoft\Report\Enums\StiRangeType', 'Stimulsoft\Report\StiRangeType');
        /** @deprecated Please use the 'Stimulsoft\Report\Enums\StiVariableType' class. */
        class_alias('Stimulsoft\Report\Enums\StiVariableType', 'Stimulsoft\Report\StiVariableType');

        /** @deprecated Please use the 'Stimulsoft\Viewer\Enums\StiChartRenderType' class. */
        class_alias('Stimulsoft\Viewer\Enums\StiChartRenderType', 'Stimulsoft\Viewer\StiChartRenderType');
        /** @deprecated Please use the 'Stimulsoft\Viewer\Enums\StiContentAlignment' class. */
        class_alias('Stimulsoft\Viewer\Enums\StiContentAlignment', 'Stimulsoft\Viewer\StiContentAlignment');
        /** @deprecated Please use the 'Stimulsoft\Viewer\Enums\StiFirstDayOfWeek' class. */
        class_alias('Stimulsoft\Viewer\Enums\StiFirstDayOfWeek', 'Stimulsoft\Viewer\StiFirstDayOfWeek');
        /** @deprecated Please use the 'Stimulsoft\Viewer\Enums\StiHtmlExportMode' class. */
        class_alias('Stimulsoft\Viewer\Enums\StiHtmlExportMode', 'Stimulsoft\Viewer\StiHtmlExportMode');
        /** @deprecated Please use the 'Stimulsoft\Viewer\Enums\StiInterfaceType' class. */
        class_alias('Stimulsoft\Viewer\Enums\StiInterfaceType', 'Stimulsoft\Viewer\StiInterfaceType');
        /** @deprecated Please use the 'Stimulsoft\Viewer\Enums\StiParametersPanelPosition' class. */
        class_alias('Stimulsoft\Viewer\Enums\StiParametersPanelPosition', 'Stimulsoft\Viewer\StiParametersPanelPosition');
        /** @deprecated Please use the 'Stimulsoft\Viewer\Enums\StiPrintDestination' class. */
        class_alias('Stimulsoft\Viewer\Enums\StiPrintDestination', 'Stimulsoft\Viewer\StiPrintDestination');
        /** @deprecated Please use the 'Stimulsoft\Viewer\Enums\StiShowMenuMode' class. */
        class_alias('Stimulsoft\Viewer\Enums\StiShowMenuMode', 'Stimulsoft\Viewer\StiShowMenuMode');
        /** @deprecated Please use the 'Stimulsoft\Viewer\Enums\StiToolbarDisplayMode' class. */
        class_alias('Stimulsoft\Viewer\Enums\StiToolbarDisplayMode', 'Stimulsoft\Viewer\StiToolbarDisplayMode');
        /** @deprecated Please use the 'Stimulsoft\Viewer\Enums\StiViewerTheme' class. */
        class_alias('Stimulsoft\Viewer\Enums\StiViewerTheme', 'Stimulsoft\Viewer\StiViewerTheme');
        /** @deprecated Please use the 'Stimulsoft\Viewer\Enums\StiWebViewMode' class. */
        class_alias('Stimulsoft\Viewer\Enums\StiWebViewMode', 'Stimulsoft\Viewer\StiWebViewMode');
        /** @deprecated Please use the 'Stimulsoft\Viewer\Enums\StiZoomMode' class. */
        class_alias('Stimulsoft\Viewer\Enums\StiZoomMode', 'Stimulsoft\Viewer\StiZoomMode');

        /** @deprecated Please use the 'Stimulsoft\Designer\Enums\StiDesignerPermissions' class. */
        class_alias('Stimulsoft\Designer\Enums\StiDesignerPermissions', 'Stimulsoft\Designer\StiDesignerPermissions');
        /** @deprecated Please use the 'Stimulsoft\Designer\Enums\StiDesignerTheme' class. */
        class_alias('Stimulsoft\Designer\Enums\StiDesignerTheme', 'Stimulsoft\Designer\StiDesignerTheme');
        /** @deprecated Please use the 'Stimulsoft\Designer\Enums\StiFirstDayOfWeek' class. */
        class_alias('Stimulsoft\Designer\Enums\StiFirstDayOfWeek', 'Stimulsoft\Designer\StiFirstDayOfWeek');
        /** @deprecated Please use the 'Stimulsoft\Designer\Enums\StiInterfaceType' class. */
        class_alias('Stimulsoft\Designer\Enums\StiInterfaceType', 'Stimulsoft\Designer\StiInterfaceType');
        /** @deprecated Please use the 'Stimulsoft\Designer\Enums\StiNewReportDictionary' class. */
        class_alias('Stimulsoft\Designer\Enums\StiNewReportDictionary', 'Stimulsoft\Designer\StiNewReportDictionary');
        /** @deprecated Please use the 'Stimulsoft\Designer\Enums\StiPropertiesGridPosition' class. */
        class_alias('Stimulsoft\Designer\Enums\StiPropertiesGridPosition', 'Stimulsoft\Designer\StiPropertiesGridPosition');
        /** @deprecated Please use the 'Stimulsoft\Designer\Enums\StiReportUnitType' class. */
        class_alias('Stimulsoft\Designer\Enums\StiReportUnitType', 'Stimulsoft\Designer\StiReportUnitType');
        /** @deprecated Please use the 'Stimulsoft\Designer\Enums\StiUseAliases' class. */
        class_alias('Stimulsoft\Designer\Enums\StiUseAliases', 'Stimulsoft\Designer\StiUseAliases');
        /** @deprecated Please use the 'Stimulsoft\Designer\Enums\StiWizardType' class. */
        class_alias('Stimulsoft\Designer\Enums\StiWizardType', 'Stimulsoft\Designer\StiWizardType');

        /** @deprecated Please use the 'Stimulsoft\Events\StiDataEventArgs' class. */
        class_alias('Stimulsoft\Events\StiDataEventArgs', 'Stimulsoft\StiDataEventArgs');
        /** @deprecated Please use the 'Stimulsoft\Events\StiExportEventArgs' class. */
        class_alias('Stimulsoft\Events\StiExportEventArgs', 'Stimulsoft\StiExportEventArgs');
        /** @deprecated Please use the 'Stimulsoft\Events\StiReportEventArgs' class. */
        class_alias('Stimulsoft\Events\StiReportEventArgs', 'Stimulsoft\StiReportEventArgs');
        /** @deprecated Please use the 'Stimulsoft\Events\StiVariablesEventArgs' class. */
        class_alias('Stimulsoft\Events\StiVariablesEventArgs', 'Stimulsoft\StiVariablesEventArgs');
    }


### Helpers

    protected function createRequest()
    {
        return new StiRequest();
    }

    protected function checkEvent(): bool
    {
        $values = StiEventType::getValues();
        return in_array($this->request->event, $values);
    }

    protected function checkCommand(): bool
    {
        if ($this->request->event == StiEventType::BeginProcessData)
            return parent::checkCommand();

        return true;
    }

    protected function updateEvents()
    {
        parent::updateEvents();

        $this->updateEvent('onPrepareVariables');
        $this->updateEvent('onBeforeRender');
        $this->updateEvent('onAfterRender');
        $this->updateEvent('onOpenReport');
        $this->updateEvent('onOpenedReport');
        $this->updateEvent('onPrintReport');
        $this->updateEvent('onBeginExportReport');
        $this->updateEvent('onEndExportReport');
        $this->updateEvent('onInteraction');
        $this->updateEvent('onEmailReport');
        $this->updateEvent('onDesignReport');
        $this->updateEvent('onCreateReport');
        $this->updateEvent('onSaveReport');
        $this->updateEvent('onSaveAsReport');
        $this->updateEvent('onPreviewReport');
        $this->updateEvent('onCloseReport');
        $this->updateEvent('onExit');
    }

    private function getComponent()
    {
        if ($this->component !== null)
            return $this->component;

        if ($this->request->sender == 'Report')
            return new StiReport();

        if ($this->request->sender == 'Viewer')
            return new StiViewer();

        if ($this->request->sender == 'Designer')
            return new StiDesigner();

        return null;
    }

    private function setHtmlRendered()
    {
        $this->htmlRendered = true;
        $GLOBALS["Stimulsoft_Scripts_stimulsoft_handler_js"] = true;
    }

    private function updateOptions()
    {
        if (StiHandler::$legacyMode)
            StiFunctions::populateObject($this, $this->options);
    }


### Results

    private function getPrepareVariablesResult(): StiResult
    {
        $this->updateEvents();

        if ($this->onPrepareVariables->getLength() > 0) {
            $args = new StiVariablesEventArgs($this->request);
            $this->onPrepareVariables->call($args);

            $result = StiResult::getSuccess();
            $result->handlerVersion = $this->version;
            $result->variables = [];

            foreach ($args->variables as $variable) {
                $isChanged = true;
                $isNew = true;
                foreach ($this->request->variables as $requestVariable) {
                    if ($variable->name == $requestVariable->name) {
                        $isNew = false;
                        if (StiVariableType::isRange($variable->type))
                            $isChanged = $variable->value->from !== $requestVariable->value->from || $variable->value->to !== $requestVariable->value->to;
                        else if (StiVariableType::isList($variable->type))
                            $isChanged = $variable->value !== $requestVariable->value; // TODO: Compare List
                        else
                            $isChanged = $variable->value !== $requestVariable->value;
                        break;
                    }
                }

                if ($isChanged || $isNew)
                    $result->variables[] = $variable;
            }
        }
        else
            $result = StiResult::getError("The handler for the 'onPrepareVariables' event is not specified.");

        return $result;
    }

    /**
     * Returns the result of processing a request from the client side. The response object will contain the data for the response,
     * as well as their MIME type, Content-Type, and other useful information to create a web server response.
     */
    public function getResponse()
    {
        return new StiResponse($this);
    }

    /**
     * Returns the result of processing a request from the client side. The result object will contain a collection of data,
     * message about the result of the command execution, and other technical information.
     */
    public function getResult()
    {
        if ($this->request->event == StiEventType::GetResource) {
            $result = StiResourcesHelper::getResult($this->request->data);
            $result->handlerVersion = $this->version;
            return $result;
        }

        if ($this->request->event == StiEventType::PrepareVariables)
            return $this->getPrepareVariablesResult();

        $component = $this->getComponent();
        if ($component != null) {

            // New component for event
            if ($this->component === null) {
                $eventName = "on{$this->request->event}";
                $component->handler = $this;
                $component->$eventName = $this->$eventName;
            }

            // Process component event
            $result = $component->getEventResult();
            if ($result != null) {
                $result->handlerVersion = $this->version;
                return $result;
            }
        }

        return parent::getResult();
    }


### JavaScript

    private function getJavaScript()
    {
        $result = StiResourcesHelper::getResult('stimulsoft.handler.js');
        if ($result->success) {
            $csrf_token = function_exists('csrf_token') ? csrf_token() : null;
            $script = $result->data ?? '';

            // Replace Handler parameters
            $script = str_replace('{databases}', StiFunctions::getJavaScriptValue(StiDatabaseType::getValues()), $script);
            $script = str_replace('{csrf_token}', StiFunctions::getJavaScriptValue($csrf_token), $script);
            $script = str_replace('{url}', StiFunctions::getJavaScriptValue($this->getUrl()), $script);
            $script = str_replace('{timeout}', StiFunctions::getJavaScriptValue($this->timeout), $script);
            $script = str_replace('{encryptData}', StiFunctions::getJavaScriptValue($this->encryptData), $script);
            $script = str_replace('{passQueryParametersToReport}', StiFunctions::getJavaScriptValue($this->passQueryParametersToReport), $script);
            $script = str_replace('{checkDataAdaptersVersion}', StiFunctions::getJavaScriptValue($this->checkDataAdaptersVersion), $script);
            $script = str_replace('{escapeQueryParameters}', StiFunctions::getJavaScriptValue($this->escapeQueryParameters), $script);
            $script = str_replace('{framework}', StiFunctions::getJavaScriptValue('PHP'), $script);

            if (StiHandler::$legacyMode)
                $script = str_replace(
                    'stimulsoft.handler = new StiHandler();',
                    'stimulsoft.handler = new StiHandler(); stimulsoft.Helper = stimulsoft.handler;',
                    $script);

            return $script;
        }

        return "// $result->notice";
    }


### HTML

    /**
     * Gets the HTML representation of the component or element.
     * @param StiHtmlMode $mode HTML code generation mode.
     * @return string Prepared HTML and JavaScript code for embedding in an HTML template.
     */
    public function getHtml($mode = StiHtmlMode::HtmlScripts): string
    {
        $this->updateOptions();

        $result = '';
        if ($mode == StiHtmlMode::HtmlScripts || $mode == StiHtmlMode::HtmlPage)
            $result .= "<script type=\"text/javascript\">\n";

        $result .= $this->getJavaScript() . "\n";

        if (StiHandler::$legacyMode) {
            if ($this->license !== null && !$this->license->htmlRendered)
                $result .= $this->license->getHtml();
        }

        if ($mode == StiHtmlMode::HtmlScripts || $mode == StiHtmlMode::HtmlPage)
            $result .= "</script>\n";

        $this->setHtmlRendered();

        return $result;
    }

    /**
     * Outputs the HTML representation of the component or element.
     */
    public function renderHtml($mode = StiHtmlMode::HtmlScripts)
    {
        if (StiHandler::$legacyMode && $mode == StiHtmlMode::HtmlScripts)
            $mode = StiHtmlMode::Scripts;

        echo $this->getHtml($mode);
    }


### Constructor

    public function __construct($url = null, $timeout = 30, $registerErrorHandlers = true)
    {
        if (StiHandler::$legacyMode) {
            $this->options = new \stdClass();
            $this->license = new StiLicense();
            if ($url === null)
                $url = 'handler.php';
        }

        parent::__construct($url, $registerErrorHandlers);

        $this->timeout = $timeout;
    }
}
