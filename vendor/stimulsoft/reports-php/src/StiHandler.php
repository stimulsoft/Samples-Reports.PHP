<?php

namespace Stimulsoft;

use PHPMailer\PHPMailer\PHPMailer;
use Stimulsoft\Adapters\StiDataAdapter;
use Stimulsoft\Report\StiVariable;
use Stimulsoft\Report\StiVariableRange;

class StiHandler extends StiDataHandler
{
    public $options;
    public $license;
    public $encryptData = true;
    public $escapeQueryParameters = true;
    public $passQueryParametersToReport = false;

    /** The event is invoked before data request, which needed to render a report. */
    public $onBeginProcessData;

    /** The event is invoked after loading data before rendering a report. */
    public $onEndProcessData;

    /** The event is invoked before rendering a report after preparing report variables. */
    public $onPrepareVariables;

    /** The event is invoked after creation a new report in the designer. */
    public $onCreateReport;

    /** The event is invoked before opening a report from the designer menu. TODO */
    public $onOpenReport;

    /** The event is invoked when saving a report in the designer. */
    public $onSaveReport;

    /** The event is invoked when saving a report in the designer with a preliminary input of the file name. */
    public $onSaveAsReport;

    /** The event is invoked before printing a report. */
    public $onPrintReport;

    /** The event is invoked before exporting a report after the dialog of export settings. */
    public $onBeginExportReport;

    /** The event is invoked after exporting a report till its saving as a file. */
    public $onEndExportReport;

    /** The event is invoked after exporting a report before sending it by Email. */
    public $onEmailReport;


    // Functions

    private function checkEventResult($event, $args)
    {
        if (isset($args) && $args->sender == null) $args->sender = StiComponentType::Report;
        if (isset($event)) $result = $event($args);
        if (!isset($result)) $result = StiResult::success();
        if ($result === true) return StiResult::success();
        if ($result === false) return StiResult::error();
        if (gettype($result) == 'string') return StiResult::error($result);
        if (isset($args)) $result->object = $args;
        return $result;
    }

    private function addAddress($param, $settings, $mail)
    {
        $arr = $settings->$param;

        if ($arr != null && count($arr) > 0) {
            if ($param == 'cc') $mail->clearCCs();
            else $mail->clearBCCs();

            foreach ($arr as $value) {
                $name = mb_strpos($value, ' ') > 0 ? mb_substr($value, mb_strpos($value, ' ')) : '';
                $address = !is_null($name) && strlen($name) > 0 ? mb_substr($value, 0, mb_strpos($value, ' ')) : $value;

                if ($param == 'cc') $mail->addCC($address, $name);
                else $mail->addBCC($address, $name);
            }
        }
    }


    // Events

    private function invokeBeginProcessData($request)
    {
        $args = new StiDataEventArgs();
        $args->populateVars($request);
        $args->parameters = $this->getParameters($request);

        return $this->checkEventResult($this->onBeginProcessData, $args);
    }

    private function invokeEndProcessData($request, $result)
    {
        $args = new StiDataEventArgs();
        $args->populateVars($request);
        $args->result = $result;

        return $this->checkEventResult($this->onEndProcessData, $args);
    }

    private function invokePrepareVariables($request)
    {
        $args = new StiVariablesEventArgs();
        $args->sender = $request->sender;

        $args->variables = array();
        if (isset($request->variables)) {
            foreach ($request->variables as $item) {
                $request->variables[$item->name] = $item;
                $variableObject = new StiVariable();
                $variableObject->name = $item->name;
                $variableObject->value = $item->value;
                $variableObject->type = $item->type;

                if (substr($item->type, -5) === 'Range') {
                    $variableObject->value = new StiVariableRange();
                    $variableObject->value->from = $item->value->from;
                    $variableObject->value->to = $item->value->to;
                }

                $args->variables[$item->name] = $variableObject;
            }
        }

        $result = $this->checkEventResult($this->onPrepareVariables, $args);

        if (isset($result->object)) {
            $variables = array();
            foreach ($result->object->variables as $key => $item) {
                // Send only changed or new values
                if (!array_key_exists($key, $request->variables) ||
                    $item->value != $request->variables[$key]->value ||
                    substr($item->type, -5) === 'Range' && (
                        $item->value->from != $request->variables[$key]->value->from ||
                        $item->value->to != $request->variables[$key]->value->to)
                ) {
                    if (!is_object($item)) $item = (object)$item;
                    $item->name = $key;
                    $variables[] = $item;
                }
            }

            $result->variables = $variables;
        }

        return $result;
    }

    private function invokeCreateReport($request)
    {
        $args = new StiReportEventArgs();
        $args->populateVars($request);

        $result = $this->checkEventResult($this->onCreateReport, $args);
        $result->report = $args->report;

        return $result;
    }

    private function invokeOpenReport($request)
    {
        $args = new StiReportEventArgs();
        $args->sender = $request->sender;
        return $this->checkEventResult($this->onOpenReport, $args);
    }

    private function invokeSaveReport($request)
    {
        $args = new StiReportEventArgs();
        $args->populateVars($request);

        return $this->checkEventResult($this->onSaveReport, $args);
    }

    private function invokeSaveAsReport($request)
    {
        $args = new StiReportEventArgs();
        $args->populateVars($request);

        return $this->checkEventResult($this->onSaveAsReport, $args);
    }

    private function invokePrintReport($request)
    {
        $args = new StiExportEventArgs();
        $args->populateVars($request);

        $args->action = $args->action == null ? StiExportAction::PrintReport : $args->action;
        $args->format = $args->printAction == StiPrintAction::PrintPdf ? StiExportFormat::Pdf : StiExportFormat::Html;
        $args->formatName = $args->printAction == StiPrintAction::PrintPdf ? 'Pdf' : 'Html';

        return $this->checkEventResult($this->onPrintReport, $args);
    }

    private function invokeBeginExportReport($request)
    {
        $args = new StiExportEventArgs();
        $args->populateVars($request);
        $args->fileExtension = StiExportFormat::getFileExtension($request->format);

        $result = $this->checkEventResult($this->onBeginExportReport, $args);
        $result->fileName = $args->fileName;
        $result->settings = $args->settings;

        return $result;
    }

    private function invokeEndExportReport($request)
    {
        $args = new StiExportEventArgs();
        $args->populateVars($request);
        $args->action = $args->action == null ? StiExportAction::ExportReport : $args->action;
        $args->fileExtension = StiExportFormat::getFileExtension($request->format);

        return $this->checkEventResult($this->onEndExportReport, $args);
    }

    private function invokeEmailReport($request)
    {
        $settings = new StiEmailSettings();
        $settings->to = $request->settings->email;
        $settings->subject = $request->settings->subject;
        $settings->message = $request->settings->message;
        $settings->attachmentName = $request->fileName . '.' . StiExportFormat::getFileExtension($request->format);

        $args = new StiExportEventArgs();
        $args->populateVars($request);
        $args->emailSettings = $settings;

        $result = $this->checkEventResult($this->onEmailReport, $args);
        if (!$result->success) return $result;

        $guid = substr(md5(uniqid() . mt_rand()), 0, 12);
        if (!file_exists('tmp')) mkdir('tmp');
        file_put_contents('tmp/' . $guid . '.' . $args->fileName, base64_decode($args->data));

        // Detect auth mode
        $auth = $settings->host != null && $settings->login != null && $settings->password != null;

        $mail = new PHPMailer(true);
        if ($auth) $mail->isSMTP();
        try {
            $mail->CharSet = $settings->charset;
            $mail->isHTML(false);
            $mail->From = $settings->from;
            $mail->FromName = $settings->name;

            // Add Emails list
            $emails = preg_split('/[,;]/', $settings->to);
            foreach ($emails as $settings->to) {
                $mail->addAddress(trim($settings->to));
            }

            // Fill email fields
            $mail->Subject = htmlspecialchars($settings->subject);
            $mail->Body = $settings->message;
            $mail->addAttachment('tmp/' . $guid . '.' . $args->fileName, $settings->attachmentName);

            // Fill auth fields
            if ($auth) {
                $mail->Host = $settings->host;
                $mail->Port = $settings->port;
                $mail->Username = $settings->login;
                $mail->Password = $settings->password;

                $mail->SMTPAuth = true;
                $mail->SMTPSecure = $settings->secure;
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
            }

            // Fill CC and BCC
            $this->addAddress('cc', $settings, $mail);
            $this->addAddress('bcc', $settings, $mail);

            $mail->Send();
        }
        catch (\Exception $e) {
            $error = strip_tags($e->getMessage());
        }

        unlink('tmp/' . $guid . '.' . $args->fileName);

        return isset($error) ? StiResult::error($error) : $result;
    }

    /** Start processing the request from the client side. */
    public function process($response = true)
    {
        $request = new StiRequest();
        $result = $request->parse();
        if ($result->success) {
            switch ($request->event) {
                case StiEventType::BeginProcessData:
                    $dataAdapter = StiDataAdapter::getDataAdapter($request->database);
                    if ($dataAdapter == null) {
                        $result = StiResult::error("Unknown database type [$request->database]");
                        break;
                    }

                    $result = $this->invokeBeginProcessData($request);
                    if (!$result->success) break;

                    $request->connectionString = $result->object->connectionString;
                    $request->queryString = $result->object->queryString;
                    $request->parameters = $result->object->parameters;

                    $result = $this->getDataAdapterResult($dataAdapter, $request);
                    if (!$result->success) break;

                    /** @var StiDataResult $result */
                    $result = $this->invokeEndProcessData($request, $result);
                    $result->adapterVersion = $dataAdapter->version;
                    $result->checkVersion = $dataAdapter->checkVersion;
                    if (!$result->success) break;

                    if (isset($result->object) && isset($result->object->result)) {
                        /** @var StiResult $result */
                        $result = $result->object->result;
                        $result->adapterVersion = $dataAdapter->version;
                        $result->checkVersion = $dataAdapter->checkVersion;
                    }
                    break;

                case StiEventType::PrepareVariables:
                    $result = $this->invokePrepareVariables($request);
                    break;

                case StiEventType::CreateReport:
                    $result = $this->invokeCreateReport($request);
                    break;

                case StiEventType::OpenReport:
                    $result = $this->invokeOpenReport($request);
                    break;

                case StiEventType::SaveReport:
                    $result = $this->invokeSaveReport($request);
                    break;

                case StiEventType::SaveAsReport:
                    $result = $this->invokeSaveAsReport($request);
                    break;

                case StiEventType::PrintReport:
                    $result = $this->invokePrintReport($request);
                    break;

                case StiEventType::BeginExportReport:
                    $result = $this->invokeBeginExportReport($request);
                    break;

                case StiEventType::EndExportReport:
                    $result = $this->invokeEndExportReport($request);
                    break;

                case StiEventType::EmailReport:
                    $result = $this->invokeEmailReport($request);
                    break;

                default:
                    $result = StiResult::error("Unknown event [$request->event]");
                    break;
            }
        }

        $result->handlerVersion = $this->version;
        if ($request->event != StiEventType::BeginProcessData) {
            unset($result->adapterVersion);
            unset($result->checkVersion);
        }

        if ($response)
            StiResponse::json($result);

        return $result;
    }

    /** Get the HTML representation of the component. */
    public function getHtml()
    {
        $csrf_token = function_exists('csrf_token') ? csrf_token() : null;

        $result = /** @lang JavaScript */
            "StiHelper.prototype.process = function (args, callback) {
                if (args) {
                    if (callback)
                        args.preventDefault = true;

                    if (args.event === 'BeginProcessData' || args.event === 'EndProcessData') {
                        if (args.database === 'XML' || args.database === 'JSON' || args.database === 'Excel')
                            return callback ? callback(null) : null;
                        if (args.database === 'Data from DataSet, DataTables')
                            return callback ? callback(args) : null;
                    }

                    let command = {};
                    for (let p in args) {
                        if (p === 'report') {
                            if (args.report && (args.event === 'CreateReport' || args.event === 'SaveReport' || args.event === 'SaveAsReport'))
                                command.report = JSON.parse(args.report.saveToJsonString());
                        } else if (p === 'settings' && args.settings) command.settings = args.settings;
                        else if (p === 'data') command.data = Stimulsoft.System.Convert.toBase64String(args.data);
                        else if (p === 'variables') command[p] = this.getVariables(args[p]);
                        else command[p] = args[p];
                    }

                    let sendText = Stimulsoft.Report.Dictionary.StiSqlAdapterService.getStringCommand(command);
                    if (!callback) callback = function (args) {
                        if (!args.success || !Stimulsoft.System.StiString.isNullOrEmpty(args.notice)) {
                            let message = Stimulsoft.System.StiString.isNullOrEmpty(args.notice) ? 'There was some error' : args.notice;
                            Stimulsoft.System.StiError.showError(message, true, args.success);
                        }
                    }
                    Stimulsoft.Helper.send(sendText, callback);
                }
            }

            StiHelper.prototype.send = function (json, callback) {
                let request = new XMLHttpRequest();
                try {
                    request.open('post', this.url, true);
                    request.setRequestHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
                    request.setRequestHeader('Cache-Control', 'max-age=0');
                    request.setRequestHeader('Pragma', 'no-cache');" . ($csrf_token ? "
                    request.setRequestHeader('X-CSRF-TOKEN', '$csrf_token');" : '') . "
                    request.timeout = this.timeout * 1000;
                    request.onload = function () {
                        if (request.status === 200) {
                            let responseText = request.responseText;
                            request.abort();

                            try {
                                let args = JSON.parse(responseText);
                                if (args.report) {
                                    let json = args.report;
                                    args.report = new Stimulsoft.Report.StiReport();
                                    args.report.load(json);
                                }

                                callback(args);
                            } catch (e) {
                                Stimulsoft.System.StiError.showError(e.message);
                            }
                        } else {
                            Stimulsoft.System.StiError.showError('Server response error: [' + request.status + '] ' + request.statusText);
                        }
                    };
                    request.onerror = function (e) {
                        let errorMessage = 'Connect to remote error: [' + request.status + '] ' + request.statusText;
                        Stimulsoft.System.StiError.showError(errorMessage);
                    };
                    request.send(json);
                } catch (e) {
                    let errorMessage = 'Connect to remote error: ' + e.message;
                    Stimulsoft.System.StiError.showError(errorMessage);
                    request.abort();
                }
            };

            StiHelper.prototype.isNullOrEmpty = function (value) {
                return value == null || value === '' || value === undefined;
            }

            StiHelper.prototype.getVariables = function (variables) {
                if (variables) {
                    for (let variable of variables) {
                        if (variable.type === 'DateTime' && variable.value != null)
                            variable.value = variable.value.toString('YYYY-MM-DD HH:mm:SS');
                    }
                }

                return variables;
            }

            function StiHelper(url, timeout) {
                this.url = url;
                this.timeout = timeout;

                if (Stimulsoft && Stimulsoft.StiOptions) {
                    Stimulsoft.StiOptions.WebServer.url = url;
                    Stimulsoft.StiOptions.WebServer.timeout = timeout;
                }
            }

            Stimulsoft = Stimulsoft || {};
            Stimulsoft.Helper = new StiHelper('{$this->options->url}', {$this->options->timeout});
            jsHelper = typeof jsHelper !== 'undefined' ? jsHelper : Stimulsoft.Helper;
            ";

        if (!$this->encryptData)
            $result .= "StiOptions.WebServer.encryptData = false;\n";

        if (!$this->escapeQueryParameters)
            $result .= "StiOptions.WebServer.escapeQueryParameters = false;\n";

        if ($this->passQueryParametersToReport)
            $result .= "StiOptions.WebServer.passQueryParametersToReport = true;\n";

        if (!$this->license->isHtmlRendered)
            $result .= $this->license->getHtml();

        return $result;
    }

    /** Output of the HTML representation of the component. */
    public function renderHtml()
    {
        echo $this->getHtml();
    }

    public function __construct($registerErrorHandlers = true)
    {
        parent::__construct($registerErrorHandlers);

        $this->options = new StiHandlerOptions();
        $this->license = new StiLicense();
    }
}
