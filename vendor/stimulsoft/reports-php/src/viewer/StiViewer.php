<?php

namespace Stimulsoft\Viewer;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Stimulsoft\Enums\StiComponentType;
use Stimulsoft\Enums\StiEventType;
use Stimulsoft\Enums\StiHtmlMode;
use Stimulsoft\Events\StiComponentEvent;
use Stimulsoft\Events\StiEmailEventArgs;
use Stimulsoft\Events\StiExportEventArgs;
use Stimulsoft\Events\StiPrintEventArgs;
use Stimulsoft\Events\StiReportEventArgs;
use Stimulsoft\Report\StiReport;
use Stimulsoft\StiComponent;
use Stimulsoft\StiEmailSettings;
use Stimulsoft\StiFunctions;
use Stimulsoft\StiHandler;
use Stimulsoft\StiResult;

class StiViewer extends StiComponent
{

### Events

    /** @var StiComponentEvent The event is invoked before opening a report from the viewer toolbar after clicking the button. Only JavaScript functions are supported. */
    public $onOpenReport;

    /** @var StiComponentEvent The event is invoked after opening a report before showing. PHP and JavaScript functions are supported. */
    public $onOpenedReport;

    /** @var StiComponentEvent The event is invoked before printing a report. PHP and JavaScript functions are supported. */
    public $onPrintReport;

    /** @var StiComponentEvent The event is invoked before exporting a report after the dialog of export settings. PHP and JavaScript functions are supported. */
    public $onBeginExportReport;

    /** @var StiComponentEvent The event is invoked after exporting a report till its saving as a file. PHP and JavaScript functions are supported. */
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


### Properties

    /** @var StiReport Gets or sets a report object for the viewer. */
    public $report;

    /** @var StiViewerOptions All viewer component options, divided by categories. */
    public $options;


### Methods: Event handlers

    private function getOpenedReportResult()
    {
        $args = new StiReportEventArgs($this->handler->request);
        $result = $this->getDefaultEventResult($this->onOpenedReport, $args);
        if ($result != null && $args->report != $this->handler->request->report)
            $result->report = $args->report;

        return $result;
    }

    private function getPrintReportResult()
    {
        $args = new StiPrintEventArgs($this->handler->request);
        $result = $this->getDefaultEventResult($this->onPrintReport, $args);
        if ($result != null) {
            if ($args->report != $this->handler->request->report)
                $result->report = $args->report;
            if ($args->pageRange != null && $args->pageRange->compareObject($this->handler->request->pageRange) === false)
                $result->pageRange = $args->pageRange;
        }

        return $result;
    }

    private function getBeginExportReportResult()
    {
        $args = new StiExportEventArgs($this->handler->request);
        $result = $this->getDefaultEventResult($this->onBeginExportReport, $args);
        if ($result != null) {
            if ($args->fileName != $this->handler->request->fileName)
                $result->fileName = $args->fileName;
            if ($args->settings != null && $args->settings->compareObject($this->handler->request->settings) === false)
                $result->settings = $args->settings;
        }

        return $result;
    }

    private function getEndExportReportResult()
    {
        $args = new StiExportEventArgs($this->handler->request);
        return $this->getDefaultEventResult($this->onEndExportReport, $args);
    }

    /*private function getInteractionResult()
    {
        $args = new StiReportEventArgs($this->handler->request);
        return $this->getDefaultEventResult($this->onInteraction, $args);
    }*/

    private function getEmailReportResult()
    {
        $args = new StiEmailEventArgs($this->handler->request);

        if ($args->settings == null)
            $args->settings = new StiEmailSettings();

        if (strlen($args->settings->attachmentName ?? '') == 0)
            $args->settings->attachmentName = StiFunctions::endsWith($args->fileName, '.' . $args->fileExtension)
                ? $args->fileName
                : $args->fileName . '.' . $args->fileExtension;

        $result = $this->getDefaultEventResult($this->onEmailReport, $args);
        if ($result == null || $result->success == false)
            return $result;

        $tempFile = 'tmp/' . StiFunctions::newGuid() . '_' . $args->fileName;
        if (!file_exists('tmp')) mkdir('tmp');
        file_put_contents($tempFile, base64_decode($args->data));

        $mailer = new PHPMailer(true);

        // Detect auth mode
        $login = strlen($args->settings->login ?? '') > 0 ? $args->settings->login : $args->settings->from;
        $auth = $args->settings->host != null && $login != null && $args->settings->password != null;
        if ($auth) $mailer->isSMTP();

        try {
            $mailer->CharSet = $args->settings->charset;
            $mailer->isHTML(false);
            $mailer->From = $args->settings->from;
            $mailer->FromName = $args->settings->name;

            // Add Emails list
            $emails = preg_split('/[,;]/', $args->settings->to);
            foreach ($emails as $args->settings->to) {
                $mailer->addAddress(trim($args->settings->to));
            }

            // Fill email fields
            $mailer->Subject = htmlspecialchars($args->settings->subject);
            $mailer->Body = $args->settings->message;
            $mailer->addAttachment($tempFile, $args->settings->attachmentName);

            // Fill auth fields
            if ($auth) {
                $mailer->Host = $args->settings->host;
                $mailer->Port = $args->settings->port;
                $mailer->Username = $login;
                $mailer->Password = $args->settings->password;

                $mailer->SMTPAuth = true;
                $mailer->SMTPSecure = $args->settings->secure;
                $mailer->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];
            }

            // Fill CC and BCC
            $this->addAddress($mailer, 'cc', $args->settings);
            $this->addAddress($mailer, 'bcc', $args->settings);

            $mailer->Send();
        }
        catch (Exception $e) {
            $error = strip_tags($e->getMessage());
            return StiResult::getError($error);
        }
        finally {
            unlink($tempFile);
        }

        return $result;
    }

    public function getEventResult()
    {
        $this->updateEvents();
        $request = $this->getRequest();

        if ($request->event == StiEventType::OpenedReport)
            return $this->getOpenedReportResult();

        if ($request->event == StiEventType::PrintReport)
            return $this->getPrintReportResult();

        if ($request->event == StiEventType::BeginExportReport)
            return $this->getBeginExportReportResult();

        if ($request->event == StiEventType::EndExportReport)
            return $this->getEndExportReportResult();

        /*
        if ($request->event == StiEventType.Interaction)
            return $this->getInteractionResult();
        */

        if ($request->event == StiEventType::EmailReport)
            return $this->getEmailReportResult();

        return parent::getEventResult();
    }


### Methods: Helpers

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

        $this->updateEvent('onOpenReport');
        $this->updateEvent('onOpenedReport');
        $this->updateEvent('onPrintReport');
        $this->updateEvent('onBeginExportReport');
        $this->updateEvent('onEndExportReport');
        $this->updateEvent('onInteraction');
        $this->updateEvent('onEmailReport');
        $this->updateEvent('onDesignReport');
    }

    public function getComponentType()
    {
        return StiComponentType::Viewer;
    }

    public function setOptions(StiViewerOptions $options)
    {
        $this->options = $options;
        $options->setComponent($this);
    }

    public function setHandler(StiHandler $handler)
    {
        parent::setHandler($handler);

        if ($handler != null) {
            $handler->onOpenReport = $this->onOpenReport;
            $handler->onOpenedReport = $this->onOpenedReport;
            $handler->onPrintReport = $this->onPrintReport;
            $handler->onBeginExportReport = $this->onBeginExportReport;
            $handler->onEndExportReport = $this->onEndExportReport;
            $handler->onInteraction = $this->onInteraction;
            $handler->onEmailReport = $this->onEmailReport;
            $handler->onDesignReport = $this->onDesignReport;
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

    private function addAddress(PHPMailer $mailer, $param, $settings)
    {
        $arr = $settings->$param;

        if ($arr != null && count($arr) > 0) {
            if ($param == 'cc') $mailer->clearCCs();
            else $mailer->clearBCCs();

            foreach ($arr as $value) {
                $value = trim($value);
                $name = mb_strpos($value, ' ') > 0 ? trim(mb_substr($value, mb_strpos($value, ' '))) : '';
                $address = strlen($name ?? '') > 0 ? trim(mb_substr($value, 0, mb_strpos($value, ' '))) : $value;

                if ($param == 'cc') $mailer->addCC($address, $name);
                else $mailer->addBCC($address, $name);
            }
        }
    }


### Methods: HTML

    protected function getComponentHtml(): string
    {
        $result = parent::getComponentHtml();

        $result .= $this->options->getHtml();
        $result .= "let $this->id = new Stimulsoft.Viewer.StiViewer({$this->options->id}, '$this->id', false);\n";

        $result .= $this->onPrepareVariables->getHtml(true);
        $result .= $this->onBeginProcessData->getHtml(true);
        $result .= $this->onEndProcessData->getHtml();
        $result .= $this->onOpenReport->getHtml();
        $result .= $this->onOpenedReport->getHtml(true);
        $result .= $this->onPrintReport->getHtml(true);
        $result .= $this->onBeginExportReport->getHtml(true);
        $result .= $this->onEndExportReport->getHtml(false, true);
        $result .= $this->onInteraction->getHtml(true, false, false);
        $result .= $this->onEmailReport->getHtml(true);
        $result .= $this->onDesignReport->getHtml(false, false, false);

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
        if ($mode == StiHtmlMode::HtmlPage) {
            $this->options->toolbar->showFullScreenButton = false;
            $this->options->appearance->fullScreenMode = true;
        }

        return parent::getHtml($mode);
    }


### Constructor

    public function __construct($id = 'viewer', StiViewerOptions $options = null)
    {
        parent::__construct();

        if (StiHandler::$legacyMode && $id instanceof StiViewerOptions) {
            $options = $id;
            $id = 'viewer';
        }

        $this->id = strlen($id ?? '') > 0 ? $id : 'viewer';
        $this->options = $options ?? new StiViewerOptions();
        $this->setOptions($this->options);
        $this->setHandler($this->handler);
    }
}