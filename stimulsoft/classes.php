<?php

class StiConnectionInfo {
	public $host = "";
	public $port = "";
	public $database = "";
	public $userId = "";
	public $password = "";
	public $charset = "";
	public $dsn = "";
	public $privilege = "";
	public $dataPath = "";
	public $schemaPath = "";
}

class StiSender {
	const Viewer = "Viewer";
	const Designer = "Designer";
}

class StiDatabaseType {
	const MySQL = "MySQL";
	const MSSQL = "MS SQL";
	const PostgreSQL = "PostgreSQL";
	const Firebird = "Firebird";
	const Oracle = "Oracle";
	const ODBC = "ODBC";
}

class StiEventType {
	const PrepareVariables = "PrepareVariables";
	const BeginProcessData = "BeginProcessData";
	const CreateReport = "CreateReport";
	const OpenReport = "OpenReport";
	const SaveReport = "SaveReport";
	const SaveAsReport = "SaveAsReport";
	const PrintReport = "PrintReport";
	const BeginExportReport = "BeginExportReport";
	const EndExportReport = "EndExportReport";
	const EmailReport = "EmailReport";
}

class StiCommand {
	const TestConnection = "TestConnection";
	const ExecuteQuery = "ExecuteQuery";
}

class StiExportFormat {
	const Pdf = 1;
	const Text = 11;
	const Excel2007 = 14;
	const Word2007 = 15;
	const Csv = 17;
	const ImageSvg = 28;
	const Html = 32;
	const Ods = 33;
	const Odt = 34;
	const Ppt2007 = 35;
	const Html5 = 36;
	const Document = 1000;
}

class StiExportAction {
	const ExportReport = 1;
	const SendEmail = 2;
}

class StiRequest {
	public $sender = null;
	public $event = null;

	public function parse() {
		$input = file_get_contents('php://input');
		
		if (strlen($input) > 0 && mb_substr($input, 0, 1) != '{')
			$input = base64_decode(str_rot13($input));
		
		$obj = json_decode($input);
		if ($obj == null) {
			$message = 'JSON parser error #'.json_last_error();
			if (function_exists('json_last_error_msg'))
				$message .= ' ('.json_last_error_msg().')';
			
			return StiResult::error($message);
		}
		
		$parameterNames = array(
			'sender', 'event', 'command', 'connectionString', 'queryString', 'database', 'dataSource', 'connection',
			'timeout', 'data', 'fileName', 'action', 'printAction', 'format', 'formatName', 'settings', 'variables',
			'parameters', 'escapeQueryParameters', 'isWizardUsed'
		);
		
		foreach ($parameterNames as $name) {
			if (isset($obj->{$name})) $this->{$name} = $obj->{$name};
		}
		
		if (!isset($obj->event) && isset($obj->command) && ($obj->command == StiCommand::TestConnection || StiCommand::ExecuteQuery))
			$this->event = StiEventType::BeginProcessData;
		
		if (isset($obj->report)) {
			$this->report = $obj->report;
			if (defined('JSON_UNESCAPED_SLASHES'))
				$this->reportJson = json_encode($this->report, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
			else {
				// for PHP 5.3
				$this->reportJson = str_replace('\/', '/', json_encode($this->report));
				$this->reportJson = preg_replace_callback('/\\\\u(\w{4})/', function ($matches) {
					return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
				}, $this->reportJson);
			}
		}
		
		return StiResult::success(null, $this);
	}
}

class StiResponse {
	public static function json($result, $exit = true) {
		unset($result->object);
		echo defined('JSON_UNESCAPED_SLASHES') ? json_encode($result, JSON_UNESCAPED_SLASHES) : json_encode($result);
		if ($exit) exit;
	}
}

class StiResult {
	public $success = true;
	public $notice = null;
	public $object = null;

	public static function success($notice = null, $object = null) {
		$result = new StiResult();
		$result->success = true;
		$result->notice = $notice;
		$result->object = $object;
		return $result;
	}

	public static function error($notice = null) {
		$result = new StiResult();
		$result->success = false;
		$result->notice = $notice;
		return $result;
	}
}

class StiEmailSettings {
	// Email address of the sender
	public $from = null;

	// Name and surname of the sender
	public $name = null;

	// Email address of the recipient
	public $to = null;

	// Email Subject
	public $subject = null;

	// Text of the Email
	public $message = null;

	// Attached file name
	public $attachmentName = null;

	// Charset for the message
	public $charset = "UTF-8";

	// Address of the SMTP server
	public $host = null;

	// Port of the SMTP server
	public $port = 465;

	// The secure connection prefix - ssl or tls
	public $secure = "ssl";

	// Login (Username or Email) */
	public $login = null;

	// Password
	public $password = null;
	
	// The array of 'cc' addresses.
	public $cc = array();
	
	// The array of 'bcc' addresses.
	public $bcc = array();
}
