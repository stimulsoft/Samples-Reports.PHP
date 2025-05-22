<?php

namespace Stimulsoft;

use Stimulsoft\Enums\StiDataType;
use Stimulsoft\Enums\StiHtmlMode;
use Stimulsoft\Events\StiComponentEvent;
use Stimulsoft\Events\StiEvent;
use Stimulsoft\Events\StiEventArgs;
use Stimulsoft\Report\StiFunctions;
use Stimulsoft\Report\StiReport;

class StiComponent extends StiElement
{

### Events

    /** @var StiComponentEvent The event is invoked before connecting to the database after all parameters have been received. Only PHP functions are supported. */
    public $onDatabaseConnect;

    /** @var StiComponentEvent The event is invoked before rendering a report after preparing report variables. PHP and JavaScript functions are supported. */
    public $onPrepareVariables;

    /** @var StiComponentEvent The event is invoked before data request, which needed to render a report. PHP and JavaScript functions are supported. */
    public $onBeginProcessData;

    /** @var StiComponentEvent The event is invoked after loading data before rendering a report. PHP and JavaScript functions are supported. */
    public $onEndProcessData;


### Fields

    /** @var bool */
    private $processRequestResult = null;

    /** @var string */
    protected $elementId;

    /** @var StiFontCollection */
    protected $fontCollection = null;

    /** @var StiFunctions */
    protected $functions = null;


### Properties

    /**
     * @var StiHandler
     * Gets or sets an event handler that controls data passed from client to server and from server to client.
     * Contains the necessary options for sending data.
     */
    public $handler;

    /** @var StiJavaScript Gets a JavaScript manager that controls the deployment of JavaScript code necessary for components to work. */
    public $javascript;

    /** @var StiLicense Gets a license manager that allows you to load a license key in various formats. */
    public $license;


### Helpers

    public function getComponentType(): ?string
    {
        return null;
    }

    public function setHandler(StiHandler $handler)
    {
        if ($handler != null) {
            $this->handler = $handler;

            // The component must be original, since it usually processes requests
            if ($handler->component == null)
                $handler->component = $this;

            $this->updateEvents();
            $handler->onDatabaseConnect = $this->onDatabaseConnect;
            $handler->onBeginProcessData = $this->onBeginProcessData;
            $handler->onEndProcessData = $this->onEndProcessData;
            $handler->onPrepareVariables = $this->onPrepareVariables;
        }
    }

    protected function updateObjects()
    {
        $this->setHandler($this->handler);
    }


### Events

    protected function updateEvents()
    {
        if ($this->onBeginProcessData === null)
            $this->onBeginProcessData = true;

        $this->updateEvent('onBeginProcessData');
        $this->updateEvent('onEndProcessData');
        $this->updateEvent('onDatabaseConnect');
        $this->updateEvent('onPrepareVariables');
    }

    protected function updateEvent(string $eventName)
    {
        if ($this->$eventName instanceof StiComponentEvent)
            return;

        $callback = is_callable($this->$eventName) || is_string($this->$eventName) || is_bool($this->$eventName) ? $this->$eventName : null;
        $this->$eventName = new StiComponentEvent($this, $eventName);
        if ($callback !== null)
            $this->$eventName->append($callback);
    }

    /** @return StiResult|null */
    public function getEventResult(): ?StiResult
    {
        return null;
    }


### Request

    /**
     * Processing an HTTP request from the client side of the component. If successful, it is necessary to return a response
     * with the processing result, which can be obtained using the 'getResponse()' function.
     * @param string|null $query The GET query string if no framework request is specified.
     * @param string|null $body The POST form data if no framework request is specified.
     * @return bool True if the request was processed successfully.
     */
    public function processRequest(?string $query = null, ?string $body = null): bool
    {
        $this->processRequestResult = $this->handler->processRequest($query, $body);
        return $this->processRequestResult;
    }

    /**
     * Processing an HTTP request from the client side of the component. After processing, it immediately prints the result and exits.
     * @param bool $printAll Printing of all processing results, or only successful ones.
     */
    public function process(bool $printAll = false)
    {
        $this->setHandler($this->handler);
        $this->handler->process($printAll);
    }

    public function getRequest(): ?StiRequest
    {
        return $this->handler != null ? $this->handler->request : null;
    }

    /**
     * Returns the result of processing a request from the client side. The response object will contain the data for the response,
     * as well as their MIME type, Content-Type, and other useful information to create a web server response.
     */
    public function getResponse(): StiResponse
    {
        if ($this->processRequestResult === false) {
            $html = $this->getHtml(StiHtmlMode::HtmlPage);
            $result = new StiFileResult($html, StiDataType::HTML);
            return new StiResponse($this->handler, $result);
        }

        $this->processRequestResult = null;
        return $this->handler->getResponse();
    }


### HTML

    protected function setHtmlRendered($value)
    {
        $this->htmlRendered = $value;
        $this->license->htmlRendered = $value;
        $this->fontCollection->htmlRendered = $value;
        $this->functions->htmlRendered = $value;
    }

    protected function getComponentHtml(): string
    {
        $result = '';

        if (!$this->license->htmlRendered)
            $result .= $this->license->getHtml();

        if (!$this->fontCollection->htmlRendered)
            $result .= $this->fontCollection->getHtml();

        if (!$this->functions->htmlRendered)
            $result .= $this->functions->getHtml();

        return $result;
    }

    /**
     * Wrapper for registering server-side data in the report.
     * @param string $renderHtml The code for building (StiReport) or assigning (StiViewer, StiDesigner) the report.
     */
    protected function getBeforeRenderCallback(string $renderHtml): string
    {
        $reportId = property_exists($this, "report") ? $this->report->id : $this->id;
        $result = $reportId . "BeforeRenderCallback = function (args) {\n";
        $result .= "if (args.report) $reportId = args.report;\n";
        $result .= "if (args.data && args.data.data) {\n";
        $result .= "$reportId.regData(args.data.name, args.data.name, args.data.data);\n";
        $result .= "if (args.data.synchronize) $reportId.dictionary.synchronize();\n";
        $result .= "}\n";
        $result .= "$renderHtml}\n";

        return $result;
    }

    /**
     * Gets the HTML representation of the component.
     * @param StiHtmlMode|int $mode HTML code generation mode.
     * @return string Prepared HTML and JavaScript code for embedding in an HTML template.
     */
    public function getHtml(int $mode = StiHtmlMode::HtmlScripts): string
    {
        $this->updateEvents();
        $this->updateObjects();

        $result = '';

        if ($mode == StiHtmlMode::HtmlPage) {
            $result .= "<!DOCTYPE html>\n<html>\n<head>\n";
            $result .= $this->javascript->getHtml();
            $result .= "</head>\n<body>\n";
        }

        if ($mode == StiHtmlMode::HtmlScripts || $mode == StiHtmlMode::HtmlPage) {
            if ($this->elementId === null)
                $result .= "<div id=\"{$this->id}Content\"></div>\n";

            $result .= "<script type=\"text/javascript\">\n";
        }

        if ($mode == StiHtmlMode::HtmlScripts || $mode == StiHtmlMode::HtmlPage) {
            $result .= "let readyStateCheckInterval = setInterval(function() {\n";
            $result .= "if (document.readyState === \"complete\") {\n";
            $result .= "clearInterval(readyStateCheckInterval);\n";
        }

        $result .= $this->getComponentHtml();

        if ($mode == StiHtmlMode::HtmlScripts || $mode == StiHtmlMode::HtmlPage) {
            $result .= "}\n}, 50);\n";
            $result .= "</script>\n";
        }

        if ($mode == StiHtmlMode::HtmlPage)
            $result .= "</body>\n</html>";

        return $result . parent::getHtml();
    }

    /**
     * Outputs the HTML representation of the component or element.
     * @param string|null $elementId The ID of the HTML element, inside which the component code will be printed.
     * If not specified, the code will be printed in the current position.
     */
    public function renderHtml(?string $elementId = null)
    {
        $this->elementId = $elementId;

        $mode = StiHandler::$legacyMode ? StiHtmlMode::Scripts : StiHtmlMode::HtmlScripts;
        echo $this->getHtml($mode);
    }

    /**
     * Immediately prints the component as an HTML page and exits.
     */
    public function printHtml()
    {
        echo $this->getHtml(StiHtmlMode::HtmlPage);
        exit();
    }


### Constructor

    public function __construct()
    {
        $this->updateEvents();

        $this->handler = new StiHandler();
        $this->javascript = new StiJavaScript($this);
        $this->license = new StiLicense();
        $this->fontCollection = new StiFontCollection();
        $this->functions = new StiFunctions();
    }
}