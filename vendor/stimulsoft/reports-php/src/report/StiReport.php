<?php

namespace Stimulsoft\Report;

use JetBrains\PhpStorm\Deprecated;
use Stimulsoft\Enums\StiHtmlMode;
use Stimulsoft\Events\StiComponentEvent;
use Stimulsoft\Export\StiDataExportSettings;
use Stimulsoft\Export\StiExportSettings;
use Stimulsoft\Export\StiHtmlExportSettings;
use Stimulsoft\Export\StiImageExportSettings;
use Stimulsoft\Report\Enums\StiEngineType;
use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\Report\Enums\StiRangeType;
use Stimulsoft\StiComponent;
use Stimulsoft\StiFunctions;
use Stimulsoft\StiHandler;
use Stimulsoft\StiNodeJs;
use Stimulsoft\StiPath;

class StiReport extends StiComponent
{

### Events

    /** @var StiComponentEvent The event is invoked called before all actions related to report rendering. Only JavaScript functions are supported. */
    public $onBeforeRender;

    /** @var StiComponentEvent The event is invoked called immediately after report rendering. Only JavaScript functions are supported. */
    public $onAfterRender;


### Properties

    /** @var StiEngineType Gets or sets the report building and export mode - on the client side in a browser window or on the server side using Node.js */
    public $engine;

    /** @var StiDictionary Gets a report data dictionary that allows you to add new variables to an existing report. */
    public $dictionary;

    /**
     * @var StiNodeJs Gets the Node.js engine, used to build and export reports on the server side.
     * Contains the necessary deployment and operation settings.
     */
    public $nodejs;


### Fields

    private $renderCalled = false;
    private $printCalled = false;
    private $exportCalled = false;
    private $openAfterExport = false;

    /** @var string */
    private $reportString;
    /** @var string */
    private $reportFile;
    /** @var string */
    private $documentString;
    /** @var string */
    private $documentFile;
    /** @var string */
    private $exportFile;
    /** @var int */
    private $exportFormat;
    /** @var StiPagesRange|string|int */
    private $pageRange;
    /** @var StiExportSettings */
    private $exportSettings;


### Helpers

    private function clearReport()
    {
        $this->reportString = null;
        $this->reportFile = null;
        $this->documentString = null;
        $this->documentFile = null;
        $this->exportFile = null;
    }

    private function loadReportFile($path)
    {
        $path = $path instanceof StiPath ? $path : new StiPath($path);
        if ($path->filePath !== null) {
            $data = file_get_contents($path->filePath);
            if ($path->fileExtension == 'mrz' || $path->fileExtension == 'mdz')
                return $data;

            $buffer = gzencode($data);
            return base64_encode($buffer);
        }

        return null;
    }

    private function saveReportFile($path, $extension, $data): bool
    {
        $path = $path instanceof StiPath ? $path : new StiPath($path);
        if ($path->directoryPath != null) {
            if ($path->fileName == null)
                $path->fileName = $this->exportFile != null ? $this->exportFile : 'Report';
            if (!StiFunctions::endsWith(strtolower($path->fileName), ".$extension"))
                $path->fileName .= ".$extension";
            $filePath = $path->directoryPath . DIRECTORY_SEPARATOR . $path->fileName;
            file_put_contents($filePath, $data);
            return true;
        }

        return false;
    }

    protected function updateEvents()
    {
        parent::updateEvents();

        $this->updateEvent('onBeforeRender');
        $this->updateEvent('onAfterRender');
    }

    public function setHandler(StiHandler $handler)
    {
        parent::setHandler($handler);

        if ($handler != null && $this->engine == StiEngineType::ServerNodeJS)
            $handler->htmlRendered = true;
    }


### HTML

    private function getLoadReportHtml(): string
    {
        if (strlen($this->documentFile ?? '') > 0)
            return "$this->id.loadDocumentFile('$this->documentFile');\n";

        if (strlen($this->documentString ?? '') > 0)
            return "$this->id.loadPackedDocument('$this->documentString');\n";

        if (strlen($this->reportFile ?? '') > 0)
            return "$this->id.loadFile('$this->reportFile');\n";

        if (strlen($this->reportString ?? '') > 0)
            return "$this->id.loadPacked('$this->reportString');\n";

        return '';
    }

    private function getNodeJsOutput(string $type, string $data): string {
        return "console.log('{$this->nodejs->id}{\"type\":\"$type\", \"data\":\"' + $data + '\"}{$this->nodejs->id}');";
    }

    private function getPrintHtml(): string
    {
        if ($this->printCalled) {
            $pageRange = $this->pageRange;
            $pageRangeHtml = '';
            $pageRangeId = '';

            if ($pageRange !== null) {
                if (!($pageRange instanceof StiPagesRange) && strlen($pageRange) > 0)
                    $pageRange = new StiPagesRange(StiRangeType::Pages, $this->pageRange);

                $pageRangeHtml = $pageRange->getHtml();
                $pageRangeId = $pageRange->id;
            }

            return "{$pageRangeHtml}report.print($pageRangeId);\n";
        }

        return '';
    }

    private function getExportHtml(): string
    {
        $result = '';
        if ($this->exportCalled) {
            $exportFileExt = StiExportFormat::getFileExtension($this->exportFormat, $this->exportSettings);
            $exportMimeType = StiExportFormat::getMimeType($this->exportFormat, $this->exportSettings);
            $exportName = StiExportFormat::getFormatName($this->exportFormat);

            $result = $this->exportSettings !== null && !$this->exportSettings->htmlRendered ? $this->exportSettings->getHtml() : "let settings = null;\n";
            $result .= "report.exportDocumentAsync(function (data) {\n";
            if ($this->engine == StiEngineType::ServerNodeJS)
                $result .= "let buffer = Buffer.from(data);\n" . $this->getNodeJsOutput('bytes', "buffer.toString('base64')") . "\n";
            else
                $result .= $this->openAfterExport
                    ? "let blob = new Blob([new Uint8Array(data)], {type: '$exportMimeType'});\nlet fileURL = URL.createObjectURL(blob);\nwindow.open(fileURL);\n"
                    : "Stimulsoft.System.StiObject.saveAs(data, '$this->exportFile.$exportFileExt', '$exportMimeType');\n";

            $result .= "}, Stimulsoft.Report.StiExportFormat.$exportName, null, settings);\n";
        }

        return $result;
    }

    private function getAfterRenderNodeHtml(): string
    {
        return "let {$this->id}String = $this->id.savePackedDocumentToString();\n" . $this->getNodeJsOutput('string', "{$this->id}String");
    }

    protected function getComponentHtml(): string
    {
        $result = parent::getComponentHtml();

        $result .= "let $this->id = new Stimulsoft.Report.StiReport();\n";

        $result .= $this->onPrepareVariables->getHtml(true);
        $result .= $this->onBeginProcessData->getHtml(true);
        $result .= $this->onEndProcessData->getHtml(false, false, false);
        $result .= $this->getLoadReportHtml();
        $result .= $this->dictionary->getHtml();
        $result .= $this->onBeforeRender->getHtml(false, false, true, true);

        if ($this->renderCalled) {
            $result .= "$this->id.renderAsync(function () {\n";
            $result .= $this->onAfterRender->getHtml(false, false, true, true);
        }

        $result .= $this->getPrintHtml();
        $result .= $this->getExportHtml();

        if ($this->renderCalled)
            $result .= "});\n";

        return $result;
    }


### Load / Save

    /**
     * Loads a report template from a file or URL address.
     * @param string $filePath The path to the .mrt file or the URL of the report template.
     * @param bool $load Loads a report file on the server side into report object.
     */
    public function loadFile(string $filePath, bool $load = false)
    {
        $this->clearReport();
        $path = new StiPath($filePath);
        $this->exportFile = $path->fileNameOnly;

        if ($load) $this->reportString = $this->loadReportFile($path);
        else $this->reportFile = preg_replace('/\\\\/', '/', $filePath);
    }

    /**
     * Loads a report template from an XML or JSON string and store it as a packed string in Base64 format.
     * @param string|object $data Report template in XML or JSON format, or JSON object.
     * @param string $fileName The name of the report file to be used for saving and exporting.
     */
    public function load($data, string $fileName = 'Report')
    {
        $this->clearReport();
        $this->exportFile = $fileName;

        if (is_object($data)) {
            $result = json_encode($data);
            $data = $result !== false ? $result : '';
        }

        $this->reportString = base64_encode(gzencode($data));
    }

    /**
     * Loads a report template from a packed string in Base64 format.
     * @param string $data Report template as a packed string in Base64 format.
     * @param string $fileName The name of the report file to be used for saving and exporting.
     */
    public function loadPacked(string $data, string $fileName = 'Report')
    {
        $this->clearReport();
        $this->exportFile = $fileName;
        $this->reportString = $data;
    }

    /**
     * Loads a rendered report from a file or URL address.
     * @param string $filePath The path to the file or the URL of the rendered report.
     * @param bool $load Loads a report file on the server side.
     */
    public function loadDocumentFile(string $filePath, bool $load = false)
    {
        $this->clearReport();
        $path = new StiPath($filePath);
        $this->exportFile = $path->fileNameOnly;

        if ($load) $this->documentString = $this->loadReportFile($path);
        else $this->documentFile = preg_replace('/\\\\/', '/', $filePath);
    }

    /**
     * Loads a rendered report from an XML or JSON string and send it as a packed string in Base64 format.
     * @param string $data Rendered report in XML or JSON format.
     * @param string $fileName The name of the report file to be used for saving and exporting.
     */
    public function loadDocument(string $data, string $fileName = 'Report')
    {
        $this->clearReport();
        $this->exportFile = $fileName;
        $this->documentString = base64_encode(gzencode($data));
    }

    /**
     * Loads a rendered report from a packed string in Base64 format.
     * @param string $data Rendered report as a packed string in Base64 format.
     * @param string $fileName The name of the report file to be used for saving and exporting.
     */
    public function loadPackedDocument(string $data, string $fileName = 'Report')
    {
        $this->clearReport();
        $this->exportFile = $fileName;
        $this->documentString = $data;
    }

    /**
     * Saves a rendered report in JSON format.
     * @param string|null $filePath The path to the .mdc file of the rendered report.
     * @return string|bool Boolean result of saving the report. If property 'filePath' not specified, the function will return JSON string of the report.
     */
    public function saveDocument(string $filePath = null)
    {
        if (strlen($this->documentString ?? '') > 0) {
            $data = gzdecode(base64_decode($this->documentString));
            return strlen($filePath ?? '') == 0 ? $data : $this->saveReportFile($filePath, 'mdc', $data);
        }

        return false;
    }

    /**
     * Saves a rendered report as packed string in Base64 format.
     * @param string|null $filePath The path to the .mdz file of the rendered report.
     * @return string|bool Boolean result of saving the report. If property 'filePath' not specified, the function will return Base64 string of the report.
     */
    public function savePackedDocument(string $filePath = null)
    {
        if (strlen($this->documentString ?? '') > 0) {
            if (strlen($filePath ?? '') == 0)
                return $this->documentString;

            $data = base64_decode($this->documentString);
            return $this->saveReportFile($filePath, 'mdz', $data);
        }

        return false;
    }


### Process

    /**
     * Builds a report, or prepares the necessary JavaScript to build the report.
     * @param string|null $callback The 'callback' argument is deprecated, please use the 'onAfterRender' event.
     * @return bool Boolean result of building a report.
     */
    public function render(
        #[Deprecated]
        string $callback = null): bool
    {
        $this->updateEvents();
        $this->renderCalled = true;

        if (StiHandler::$legacyMode && strlen($callback ?? '') > 0)
            $this->onAfterRender->append($callback);

        if ($this->engine == StiEngineType::ServerNodeJS) {
            $afterRenderScript = $this->getAfterRenderNodeHtml();
            $this->onAfterRender->append($afterRenderScript);
            $script = $this->getHtml(StiHtmlMode::Scripts);
            $this->onAfterRender->remove($afterRenderScript);
            $this->renderCalled = false;

            $result = $this->nodejs->run($script);
            if ($result === false)
                return false;

            $this->documentString = $result;
        }

        return true;
    }

    /**
     * Prepares the necessary JavaScript to print the report. The browser print dialog will be called.
     * @param StiPagesRange|string|int|null $pageRange The page range or the page number to print.
     */
    public function print($pageRange = null)
    {
        $this->printCalled = true;
        $this->pageRange = $pageRange;
    }

    /**
     * @deprecated Please use the 'print()' method.
     */
    public function printReport($pageRange = null)
    {
        $this->print($pageRange);
    }

    /**
     * Exports the rendered report to the specified format, or prepares the necessary JavaScript to export the report.
     *
     * Important: The export function does not automatically build the report template.
     *
     * @param StiExportFormat|int $format [enum] Report export format.
     * @param StiExportSettings $settings Export settings, the type of settings must match the export format.
     * @param bool $openAfterExport Automatically open the exported report in a browser window if the export is performed on the client side.
     * @param string|null $filePath The path to the file of the exported document. It only works with server-side Node.js mode.
     * @return string|bool Byte data of the exported report, or the boolean result of the export.
     */
    public function exportDocument(int $format, StiExportSettings $settings = null, bool $openAfterExport = false, string $filePath = null)
    {
        if (StiHandler::$legacyMode && is_bool($settings)) {
            $openAfterExport = $settings;
            $settings = null;
        }

        $this->exportCalled = true;
        $this->openAfterExport = $openAfterExport;
        $this->exportFormat = $format;

        $this->exportSettings = $settings;
        if ($settings !== null) {
            if ($settings instanceof StiHtmlExportSettings && $settings->htmlType === null)
                $settings->setHtmlType($format);

            if ($settings instanceof StiDataExportSettings && $settings->dataType === null)
                $settings->setDataType($format);

            if ($settings instanceof StiImageExportSettings && $settings->imageType === null)
                $settings->setImageType($format);

            $this->exportFormat = $settings->getExportFormat();
        }

        if ($this->engine == StiEngineType::ServerNodeJS) {
            $script = $this->getHtml(StiHtmlMode::Scripts);
            $this->exportCalled = false;
            $result = $this->nodejs->run($script);

            if ($result && strlen($filePath ?? '') > 0) {
                $extension = StiExportFormat::getFileExtension($format, $settings);
                $result = $this->saveReportFile($filePath, $extension, $result);
            }

            return $result;
        }

        return true;
    }


### Constructor

    public function __construct($id = 'report')
    {
        parent::__construct();

        $this->id = strlen($id ?? '') > 0 ? $id : 'report';
        $this->dictionary = new StiDictionary($this);
        $this->nodejs = new StiNodeJs($this);
        $this->setHandler($this->handler);
    }
}