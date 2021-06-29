<?php
require_once 'classes.php';
require_once 'adapters/mysql.php';
require_once 'adapters/mssql.php';
require_once 'adapters/firebird.php';
require_once 'adapters/postgresql.php';
require_once 'adapters/oracle.php';
require_once 'adapters/odbc.php';

if (substr(PHP_VERSION, 0, 1) == '5') {
	require_once 'phpmailer/v5/class.phpmailer.php';
	require_once 'phpmailer/v5/class.pop3.php';
	require_once 'phpmailer/v5/class.smtp.php';
	require_once 'phpmailer/v5/PHPMailerAutoload.php';
}
else {
	require_once 'phpmailer/v6/PHPMailer.php';
	require_once 'phpmailer/v6/SMTP.php';
	require_once 'phpmailer/v6/POP3.php';
	require_once 'phpmailer/v6/Exception.php';
}

function stiErrorHandler($errNo, $errStr, $errFile, $errLine) {
	$result = StiResult::error("[$errNo] $errStr ($errFile, Line $errLine)");
	StiResponse::json($result);
}

function stiShutdownFunction() {
	$err = error_get_last();
	if ($err != null && (($err['type'] & E_COMPILE_ERROR) || ($err['type'] & E_ERROR) || ($err['type'] & E_CORE_ERROR) || ($err['type'] & E_RECOVERABLE_ERROR))) {
		$result = StiResult::error("[{$err['type']}] {$err['message']} ({$err['file']}, Line {$err['line']})");
		StiResponse::json($result);
	}
}

class StiHandler {
	
	private function checkEventResult($event, $args) {
		if (isset($event)) $result = $event($args);
		if (!isset($result)) $result = StiResult::success();
		if ($result === true) return StiResult::success();
		if ($result === false) return StiResult::error();
		if (gettype($result) == 'string') return StiResult::error($result);
		if (isset($args)) $result->object = $args;
		return $result;
	}
	
	private function applyQueryParameters($query, $parameters, $escape) {
		$result = '';
		
		while (mb_strpos($query, '@') !== false) {
			$result .= mb_substr($query, 0, mb_strpos($query, '@'));
			$query = mb_substr($query, mb_strpos($query, '@') + 1);
			
			$parameterName = '';
			while (strlen($query) > 0) {
				$char = mb_substr($query, 0, 1);
				if (!preg_match('/[a-zA-Z0-9_-]/', $char)) break;
				
				$parameterName .= $char;
				$query = mb_substr($query, 1);
			}
			
			$replaced = false;
			foreach ($parameters as $key => $item) {
				if (strtolower($key) == strtolower($parameterName)) {
					switch ($item->typeGroup) {
						case 'number':
							$result .= $item->value;
							break;
							
						case 'datetime':
							$result .= "'".$item->value."'";
							break;
							
						default:
							$result .= "'".($escape ? addcslashes($item->value, "\\\"'") : $item->value)."'";
							break;
					}
					
					$replaced = true;
				}
			}
			
			if (!$replaced) $result .= '@'.$parameterName;
		}
		
		return $result.$query;
	}
	
	private function addAddress($param, $settings, $mail) {
		$arr = $settings->$param;
		
		if ($arr != null && count($arr) > 0) {
			if ($param == 'cc') $mail->clearCCs();
			else $mail->clearBCCs();
			
			foreach ($arr as $value) {
				$name = mb_strpos($value, ' ') > 0 ? mb_substr($value, mb_strpos($value, ' ')) : '';
				$address = strlen($name) > 0 ? mb_substr($value, 0, mb_strpos($value, ' ')) : $value;
				
				if ($param == 'cc') $mail->addCC($address, $name);
				else $mail->addBCC($address, $name);
			}
		}
	}
	
	
// Events

	public $onPrepareVariables = null;
	private function invokePrepareVariables($request) {
		$args = new stdClass();
		$args->sender = $request->sender;
		
		$args->variables = array();
		if (isset($request->variables)) {
			foreach ($request->variables as $item) {
				$request->variables[$item->name] = $item;
				$variableObject = new stdClass();
				$variableObject->value = $item->value;
				$variableObject->type = $item->type;
				
				if (substr($item->type, -5) === 'Range') {
					$variableObject->value = new stdClass();
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
					array_push($variables, $item);
				}
			}
			
			$result->variables = $variables;
		}
		
		return $result;
	}

	public $onBeginProcessData = null;
	private function invokeBeginProcessData($request) {
		$args = new stdClass();
		$args->sender = $request->sender;
		$args->command = $request->command;
		$args->database = $request->database;
		$args->connectionString = isset($request->connectionString) ? $request->connectionString : null;
		$args->queryString = isset($request->queryString) ? $request->queryString : null;
		$args->dataSource = isset($request->dataSource) ? $request->dataSource : null;
		$args->connection = isset($request->connection) ? $request->connection : null;
		if (isset($request->queryString) && isset($request->parameters)) {
			$args->parameters = array();
			foreach ($request->parameters as $item) {
				$args->parameters[$item->name] = $item;
				unset($item->name);
			}
		}
		
		$result = $this->checkEventResult($this->onBeginProcessData, $args);
		if (isset($result->object->queryString) && isset($args->parameters) && count($args->parameters) > 0)
			$result->object->queryString = $this->applyQueryParameters($result->object->queryString, $args->parameters, $request->escapeQueryParameters);
		
		return $result;
	}
	
	public $onEndProcessData = null;
	private function invokeEndProcessData($request, $result) {
		$args = new stdClass();
		$args->sender = $request->sender;
		$args->command = $request->command;
		$args->database = $request->database;
		$args->dataSource = isset($request->dataSource) ? $request->dataSource : null;
		$args->connection = isset($request->connection) ? $request->connection : null;
		$args->result = $result;
		return $this->checkEventResult($this->onEndProcessData, $args);
	}
	
	public $onCreateReport = null;
	private function invokeCreateReport($request) {
		$args = new stdClass();
		$args->sender = $request->sender;
		$args->report = $request->report;
		$args->isWizardUsed = $request->isWizardUsed;
		
		$result = $this->checkEventResult($this->onCreateReport, $args);
		$result->report = $args->report;
		
		return $result;
	}
	
	public $onOpenReport = null;
	private function invokeOpenReport($request) {
		$args = new stdClass();
		$args->sender = $request->sender;
		return $this->checkEventResult($this->onOpenReport, $args);
	}
	
	public $onSaveReport = null;
	private function invokeSaveReport($request) {
		$args = new stdClass();
		$args->sender = $request->sender;
		$args->report = $request->report;
		$args->reportJson = $request->reportJson;
		$args->fileName = $request->fileName;
		return $this->checkEventResult($this->onSaveReport, $args);
	}
	
	public $onSaveAsReport = null;
	private function invokeSaveAsReport($request) {
		$args = new stdClass();
		$args->sender = $request->sender;
		$args->report = $request->report;
		$args->reportJson = $request->reportJson;
		$args->fileName = $request->fileName;
		return $this->checkEventResult($this->onSaveAsReport, $args);
	}
	
	public $onPrintReport = null;
	private function invokePrintReport($request) {
		$args = new stdClass();
		$args->sender = $request->sender;
		$args->fileName = $request->fileName;
		$args->printAction = $request->printAction;
		return $this->checkEventResult($this->onPrintReport, $args);
	}
	
	public $onBeginExportReport = null;
	private function invokeBeginExportReport($request) {
		$args = new stdClass();
		$args->sender = $request->sender;
		$args->action = $request->action;
		$args->format = $request->format;
		$args->formatName = $request->formatName;
		$args->settings = $request->settings;
		$args->fileName = $request->fileName;
		
		$result = $this->checkEventResult($this->onBeginExportReport, $args);
		$result->fileName = $args->fileName;
		$result->settings = $args->settings;
		
		return $result;
	}
	
	public $onEndExportReport = null;
	private function invokeEndExportReport($request) {
		$args = new stdClass();
		$args->sender = $request->sender;
		$args->format = $request->format;
		$args->formatName = $request->formatName;
		$args->fileName = $request->fileName;
		$args->fileExtension = $this->getFileExtension($request->format);
		$args->data = $request->data;
		return $this->checkEventResult($this->onEndExportReport, $args);
	}
	
	public $onEmailReport = null;
	private function invokeEmailReport($request) {
		$settings = new StiEmailSettings();
		$settings->to = $request->settings->email;
		$settings->subject = $request->settings->subject;
		$settings->message = $request->settings->message;
		$settings->attachmentName = $request->fileName.'.'.$this->getFileExtension($request->format);
		
		$args = new stdClass();
		$args->sender = $request->sender;
		$args->settings = $settings;
		$args->format = $request->format;
		$args->formatName = $request->formatName;
		$args->fileName = $request->fileName;
		$args->data = base64_decode($request->data);
		
		$result = $this->checkEventResult($this->onEmailReport, $args);
		if (!$result->success) return $result;
		
		$guid = substr(md5(uniqid().mt_rand()), 0, 12);
		if (!file_exists('tmp')) mkdir('tmp');
		file_put_contents('tmp/'.$guid.'.'.$args->fileName, $args->data);
		
		// Detect auth mode
		$auth = $settings->host != null && $settings->login != null && $settings->password != null;
		
		$mail = substr(PHP_VERSION, 0, 1) == '5' ? new PHPMailer(true) : new PHPMailer\PHPMailer\PHPMailer(true);
		if ($auth) $mail->IsSMTP();
		try {
			$mail->CharSet = $settings->charset;
			$mail->IsHTML(false);
			$mail->From = $settings->from;
			$mail->FromName = $settings->name;
				
			// Add Emails list
			$emails = preg_split('/,|;/', $settings->to);
			foreach ($emails as $settings->to) {
				$mail->AddAddress(trim($settings->to));
			}
			
			// Fill email fields
			$mail->Subject = htmlspecialchars($settings->subject);
			$mail->Body = $settings->message;
			$mail->AddAttachment('tmp/'.$guid.'.'.$args->fileName, $settings->attachmentName);
			
			// Fill auth fields
			if ($auth) {
				$mail->Host = $settings->host;
				$mail->Port = $settings->port;
				$mail->SMTPAuth = true;
				$mail->SMTPSecure = $settings->secure;
				$mail->Username = $settings->login;
				$mail->Password = $settings->password;
			}
			
			// Fill CC and BCC
			$this->addAddress('cc', $settings, $mail);
			$this->addAddress('bcc', $settings, $mail);
			
			$mail->Send();
		}
		catch (phpmailerException $e) {
			$error = strip_tags($e->errorMessage());
			return StiResult::error($error);
		}
		catch (Exception $e) {
			$error = strip_tags($e->getMessage());
		}
		
		unlink('tmp/'.$guid.'.'.$args->fileName);
		
		if (isset($error)) return StiResult::error($error);
		return $result;
	}
	
	
// Methods
	
	public function registerErrorHandlers() {
		error_reporting(0);
		set_error_handler('stiErrorHandler');
		register_shutdown_function('stiShutdownFunction');
	}
	
	public function process($response = true) {
		$result = $this->innerProcess();
		if ($response) StiResponse::json($result);
		return $result;
	}
	
	
// Private methods
	
	private function createConnection($args) {
		switch ($args->database) {
			case StiDatabaseType::MySQL: $connection = new StiMySqlAdapter(); break;
			case StiDatabaseType::MSSQL: $connection = new StiMsSqlAdapter(); break;
			case StiDatabaseType::Firebird: $connection = new StiFirebirdAdapter(); break;
			case StiDatabaseType::PostgreSQL: $connection = new StiPostgreSqlAdapter(); break;
			case StiDatabaseType::Oracle: $connection = new StiOracleAdapter(); break;
			case StiDatabaseType::ODBC: $connection = new StiOdbcAdapter(); break;
		}
		
		if (isset($connection)) {
			$connection->parse($args->connectionString);
			return StiResult::success(null, $connection);
		}
		
		return StiResult::error("Unknown database type [".$args->database."]");
	}
	
	private function innerProcess() {
		$request = new StiRequest();
		$result = $request->parse();
		if ($result->success) {
			switch ($request->event) {
				case StiEventType::BeginProcessData:
					$result = $this->invokeBeginProcessData($request);
					if (!$result->success) return $result;
					$queryString = $result->object->queryString;
					$result = $this->createConnection($result->object);
					if (!$result->success) return $result;
					$connection = $result->object;
					
					switch ($request->command) {
						case StiCommand::TestConnection:
							$result = $connection->test();
							break;
							
						case StiCommand::ExecuteQuery:
							$result = $connection->execute($queryString);
							break;
					}
					
					$result = $this->invokeEndProcessData($request, $result);
					if (!$result->success) return $result;
					if (isset($result->object) && isset($result->object->result)) return $result->object->result;
					return $result;
					
				case StiEventType::PrepareVariables:
					return $this->invokePrepareVariables($request);
					
				case StiEventType::CreateReport:
					return $this->invokeCreateReport($request);
					
				case StiEventType::OpenReport:
					return $this->invokeOpenReport($request);
					
				case StiEventType::SaveReport:
					return $this->invokeSaveReport($request);
					
				case StiEventType::SaveAsReport:
					return $this->invokeSaveReport($request);
					
				case StiEventType::PrintReport:
					return $this->invokePrintReport($request);
					
				case StiEventType::BeginExportReport:
					return $this->invokeBeginExportReport($request);
					
				case StiEventType::EndExportReport:
					return $this->invokeEndExportReport($request);
						
				case StiEventType::EmailReport:
					return $this->invokeEmailReport($request);
			}
			
			$result = StiResult::error("Unknown event [".$request->event."]");
		}
		
		return $result;
	}
	
	private function getFileExtension($format) {
		switch ($format) {
			case StiExportFormat::Pdf:
				return "pdf";
				
			case StiExportFormat::Text:
				return "txt";
				
			case StiExportFormat::Excel2007:
				return "xlsx";
				
			case StiExportFormat::Word2007:
				return "docx";
				
			case StiExportFormat::Csv:
				return "csv";
				
			case StiExportFormat::ImageSvg:
				return "svg";
				
			case StiExportFormat::Html:
			case StiExportFormat::Html5:
				return "html";
				
			case StiExportFormat::Ods:
				return "ods";
				
			case StiExportFormat::Odt:
				return "odt";
				
			case StiExportFormat::Ppt2007:
				return "pptx";
				
			case StiExportFormat::Document:
				return "mdc";
		}
		
		return $format;
	}
}


// JavaScript helper

class StiHelper {
	public static function createOptions() {
		$options = new stdClasS();
		$options->handler = "handler.php";
		$options->timeout = 30;
		
		return $options;
	}
	
	public static function initialize($options) {
		if (!isset($options)) $options = StiHelper::createOptions();
		StiHelper::init($options->handler, $options->timeout);
	}
	
	public static function init($handler, $timeout) {?>
<script type="text/javascript">
	StiHelper.prototype.process = function (args, callback) {
		if (args) {
			if (callback)
				args.preventDefault = true;
			
			if (args.event == 'BeginProcessData') {
				if (args.database == 'XML' || args.database == 'JSON' || args.database == 'Excel')
					return callback(null);
				if (args.database == 'Data from DataSet, DataTables')
					return callback(args);
			}
			
			var command = {};
			for (var p in args) {
				if (p == 'report') {
					if (args.report && (args.event == 'CreateReport' || args.event == 'SaveReport' || args.event == 'SaveAsReport'))
						command.report = JSON.parse(args.report.saveToJsonString());
				}
				else if (p == 'settings' && args.settings) command.settings = args.settings;
				else if (p == 'data') command.data = Stimulsoft.System.Convert.toBase64String(args.data);
				else if (p == 'variables') command[p] = this.getVariables(args[p]);
				else command[p] = args[p];
			}
			
			var sendText = Stimulsoft.Report.Dictionary.StiSqlAdapterService.getStringCommand(command);
			if (!callback) callback = function (args) {
				if (!args.success || !Stimulsoft.System.StiString.isNullOrEmpty(args.notice)) {
					var message = Stimulsoft.System.StiString.isNullOrEmpty(args.notice) ? 'There was some error' : args.notice;
					Stimulsoft.System.StiError.showError(message, true, args.success);
				}
			}
			Stimulsoft.Helper.send(sendText, callback);
		}
	}
	
	StiHelper.prototype.send = function (json, callback) {
		try {
			var request = new XMLHttpRequest();
			request.open('post', this.url, true);
			request.setRequestHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
			request.setRequestHeader('Cache-Control', 'max-age=0');
			request.setRequestHeader('Pragma', 'no-cache');
			request.timeout = this.timeout * 1000;
			request.onload = function () {
				if (request.status == 200) {
					var responseText = request.responseText;
					request.abort();
					
					try {
						var args = JSON.parse(responseText);
						if (args.report) {
							var json = args.report;
							args.report = new Stimulsoft.Report.StiReport();
							args.report.load(json);
						}
						
						callback(args);
					}
					catch (e) {
						Stimulsoft.System.StiError.showError(e.message);
					}
				}
				else {
					Stimulsoft.System.StiError.showError('Server response error: [' + request.status + '] ' + request.statusText);
				}
			};
			request.onerror = function (e) {
				var errorMessage = 'Connect to remote error: [' + request.status + '] ' + request.statusText;
				Stimulsoft.System.StiError.showError(errorMessage);
			};
			request.send(json);
		}
		catch (e) {
			var errorMessage = 'Connect to remote error: ' + e.message;
			Stimulsoft.System.StiError.showError(errorMessage);
			request.abort();
		}
	};
	
	StiHelper.prototype.isNullOrEmpty = function (value) {
		return value == null || value === '' || value === undefined;
	}
	
	StiHelper.prototype.getVariables = function (variables) {
		if (variables) {
			for (var variable of variables) {
				if (variable.type == 'DateTime' && variable.value != null)
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
		
		if (Stimulsoft && Stimulsoft.Base) {
			Stimulsoft.Base.StiLicense.loadFromFile("stimulsoft/license.php");
		}
	}

	Stimulsoft = Stimulsoft || {};
	Stimulsoft.Helper = new StiHelper('<?php echo $handler; ?>', <?php echo $timeout; ?>);
	jsHelper = typeof jsHelper !== 'undefined' ? jsHelper : Stimulsoft.Helper;
</script>
<?php
	}
	
	public static function createHandler() {
		?>Stimulsoft.Helper.process(arguments[0], arguments[1]);<?php
	}
}