<?php

namespace Stimulsoft\Report;

use Stimulsoft\StiExportFormat;
use Stimulsoft\StiHtmlComponent;

class StiReport extends StiHtmlComponent
{
    /** The event is invoked called before all actions related to report rendering. */
    public $onBeforeRender;

    /** The event is invoked before rendering a report after preparing report variables. */
    public $onPrepareVariables;

    /** The event is invoked before data request, which needed to render a report. */
    public $onBeginProcessData;

    /** The event is invoked after loading data before rendering a report. */
    public $onEndProcessData;

    /** @var StiDictionary Report data dictionary. */
    public $dictionary;

    private $isRenderCalled = false;
    private $isPrintCalled = false;
    private $isExportCalled = false;
    private $isOpenAfterExport = false;

    private $reportString;
    private $reportFile;
    private $documentString;
    private $documentFile;

    private $renderCallback;
    private $pagesRange;
    private $exportFormat;
    private $exportFile;

    private function clearReport()
    {
        $this->reportString = null;
        $this->reportFile = null;
        $this->documentString = null;
        $this->documentFile = null;
        $this->exportFile = null;
    }

    /**
     * Loading a report template from a file or URL address.
     * @param string $filePath The path to the file or the URL of the report template.
     * @param bool $load Loading a report file on the server side.
     */
    public function loadFile($filePath, $load = false)
    {
        $this->clearReport();
        $this->exportFile = pathinfo($filePath, PATHINFO_FILENAME);
        if ($load) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            if (file_exists($filePath) && $extension == 'mrt') {
                $reportString = file_get_contents($filePath);
                $this->reportString = base64_encode(gzencode($reportString));
            }
        }
        else {
            $this->reportFile = $filePath;
        }
    }

    /**
     * Loading a report template from an XML or JSON string and send it as a packed string in Base64 format.
     * @param string $data Report template in XML or JSON format.
     * @param string $fileName The name of the report file to be used for saving and exporting.
     */
    public function load($data, $fileName = 'Report')
    {
        $this->clearReport();
        $this->exportFile = $fileName;
        $this->reportString = base64_encode(gzencode($data));
    }

    /**
     * Loading a report template from a packed string in Base64 format.
     * @param string $data Report template as a packed string in Base64 format.
     * @param string $fileName The name of the report file to be used for saving and exporting.
     */
    public function loadPacked($data, $fileName = 'Report')
    {
        $this->clearReport();
        $this->exportFile = $fileName;
        $this->reportString = $data;
    }

    /**
     * Load a rendered report from a file or URL address.
     * @param string $filePath The path to the file or the URL of the rendered report.
     * @param bool $load Loading a report file on the server side.
     */
    public function loadDocumentFile($filePath, $load = false)
    {
        $this->clearReport();
        $this->exportFile = pathinfo($filePath, PATHINFO_FILENAME);
        if ($load) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            if (file_exists($filePath) && $extension == 'mdc') {
                $documentString = file_get_contents($filePath);
                $this->documentString = base64_encode(gzencode($documentString));
            }
        }
        else {
            $this->documentFile = $filePath;
        }
    }

    /**
     * Load a rendered report from an XML or JSON string and send it as a packed string in Base64 format.
     * @param string $data Rendered report in XML or JSON format.
     * @param string $fileName The name of the report file to be used for saving and exporting.
     */
    public function loadDocument($data, $fileName = 'Report')
    {
        $this->clearReport();
        $this->exportFile = $fileName;
        $this->documentString = base64_encode(gzencode($data));
    }

    /**
     * Loading a rendered report from a packed string in Base64 format.
     * @param string $data Rendered report as a packed string in Base64 format.
     * @param string $fileName The name of the report file to be used for saving and exporting.
     */
    public function loadPackedDocument($data, $fileName = 'Report')
    {
        $this->clearReport();
        $this->exportFile = $fileName;
        $this->documentString = $data;
    }

    /**
     * Exporting the report to the specified format and saving it as a file on the client side.
     * @param int $format The type of the export. Is equal to one of the values of the StiExportFormat enumeration.
     */
    public function exportDocument($format, $openAfterExport = false)
    {
        $this->isExportCalled = true;
        $this->isOpenAfterExport = $openAfterExport;
        $this->exportFormat = $format;
    }

    /** Building a report and calling a JavaScript callback function, if it is set. */
    public function render($callback = null)
    {
        $this->isRenderCalled = true;
        $this->renderCallback = $callback;
    }

    /** Printing the rendered report. The browser print dialog will be called. */
    public function printReport($pagesRange = null)
    {
        $this->isPrintCalled = true;
        $this->pagesRange = $pagesRange;
    }

    private function getBeforeRenderEventHtml()
    {
        $function = $this->onBeforeRender === true ? 'onBeforeRender' : $this->onBeforeRender;
        $args = "{ event: 'BeforeRender', sender: 'Report', report: $this->id }";
        return "if (typeof $function === 'function') $function($args);\n";
    }

    /** Get the HTML representation of the component. */
    public function getHtml()
    {
        $result = "let $this->id = new Stimulsoft.Report.StiReport();\n";

        if ($this->onPrepareVariables)
            $result .= $this->getEventHtml('onPrepareVariables', true);

        if ($this->onBeginProcessData)
            $result .= $this->getEventHtml('onBeginProcessData', true);

        if ($this->onEndProcessData)
            $result .= $this->getEventHtml('onEndProcessData');

        if (!is_null($this->reportFile) && strlen($this->reportFile) > 0)
            $result .= "$this->id.loadFile('$this->reportFile');\n";

        else if (!is_null($this->reportString) && strlen($this->reportString) > 0)
            $result .= "$this->id.loadPacked('$this->reportString');\n";

        else if (!is_null($this->documentFile) && strlen($this->documentFile) > 0)
            $result .= "$this->id.loadDocumentFile('$this->documentFile');\n";

        else if (!is_null($this->documentString) && strlen($this->documentString) > 0)
            $result .= "$this->id.loadPackedDocument('$this->documentString');\n";

        $result .= $this->dictionary->getHtml();

        if ($this->onBeforeRender)
            $result .= $this->getBeforeRenderEventHtml();

        if ($this->isRenderCalled) {
            $result .= "$this->id.renderAsync(function () {\n";

            if ($this->renderCallback != null)
                $result .= "$this->renderCallback();\n";
        }

        if ($this->isPrintCalled) {
            $pagesRangeId = '';
            if ($this->pagesRange instanceof StiPagesRange) {
                $pagesRangeId = $this->pagesRange->id;
                $result .= $this->pagesRange->getHtml();
            }

            $result .= "report.print($pagesRangeId);\n";
        }

        if ($this->isExportCalled) {
            $exportFileExt = StiExportFormat::getFileExtension($this->exportFormat);
            $exportMimeType = StiExportFormat::getMimeType($this->exportFormat);
            $exportName = StiExportFormat::getFormatName($this->exportFormat);

            $result .= "report.exportDocumentAsync(function (data) {\n";
            $result .= $this->isOpenAfterExport
                ? "var blob = new Blob([new Uint8Array(data)], { type: '$exportMimeType' });
                   var fileURL = URL.createObjectURL(blob);
                   window.open(fileURL);\n"
                : "Stimulsoft.System.StiObject.saveAs(data, '$this->exportFile.$exportFileExt', '$exportMimeType');\n";
            $result .= "}, Stimulsoft.Report.StiExportFormat.$exportName);\n";
        }

        if ($this->isRenderCalled) {
            $result .= "});\n";
        }

        $this->isHtmlRendered = true;
        return $result;
    }

    public function __construct($id = 'report')
    {
        $this->id = !is_null($id) && strlen($id) > 0 ? $id : 'report';
        $this->dictionary = new StiDictionary($this);
    }
}