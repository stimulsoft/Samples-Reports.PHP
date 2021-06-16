<?php

namespace Stimulsoft;

class Handler
{

	// Events

	public $onBeginExportReport = null;

	public $onBeginProcessData = null;

	public $onCreateReport = null;

	public $onEmailReport = null;

	public $onEndExportReport = null;

	public $onEndProcessData = null;

	public $onOpenReport = null;

	public $onPrepareVariables = null;

	public $onPrintReport = null;

	public $onSaveAsReport = null;

	public $onSaveReport = null;

	private $phpMailer = null;

	/**
	 * Return the JavaScript needed to call the server side functions set in this class
	 *
	 * All events and their details can be found in the documentation at:
	 * @link https://www.stimulsoft.com/en/documentation/online/programming-manual/index.html?reports_js_web_designer_designer_events.htm
	 *
	 * @param string $variableName the name of the JavaScript variable to set
	 *
	 * @return string of JavaScript needed to set the server side callbacks
	 */
	public function getServerFunctionsJavaScript($variableName)
		{
		$js = '';
		$reflection = new \ReflectionClass($this);
		foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property)
			{
			$name = $property->getName();
			if (0 === strpos($name, 'on'))
				{
				if ('onEndProcessData' != $name && null !== $this->$name)
					{
					$js .= "\n" . $variableName . '.' . $name . ' = function (args, callback) {args.preventDefault = true;
						Stimulsoft.Helper.process(args, callback);}' . "\n";
					}
				}
			}

		return $js;
		}

	/**
	 * getMailer returns the mailer instance to use. Override to change behavior or modify settings.
	 *
	 * @param \Stimulsoft\EmailSettings $settings that will be used for PHPMailer.  Can be queried or changed as needed in child class.
	 *
	 * @return \Stimulsoft\Interfaces\Mailer
	 */
	public function getMailer(\Stimulsoft\EmailSettings $settings)
	{
		if (! $this->phpMailer) {
			$this->phpMailer = '5' == \substr(PHP_VERSION, 0, 1) ? new \PHPMailer(true) : new \PHPMailer\PHPMailer\PHPMailer(true);
		}

		return clone $this->phpMailer;
	}

	// Methods

	/**
	 * Handle errors
	 *
	 * @param int $errNo PHP error number
	 * @param string $errStr PHP error text
	 * @param string $errFile file containing error
	 * @param int $errLine line number in $errFile
	 *
	 */
	public function errorHandler($errNo, $errStr, $errFile, $errLine)
	{
		$result = \Stimulsoft\Result::error("[{$errNo}] {$errStr} ({$errFile}, Line {$errLine})");
		\Stimulsoft\Response::json($result);
	}

	public function shutdownFunction()
	{
		$err = \error_get_last();

		if (null != $err && (($err['type'] & E_COMPILE_ERROR) || ($err['type'] & E_ERROR) || ($err['type'] & E_CORE_ERROR) || ($err['type'] & E_RECOVERABLE_ERROR))) {
			$result = \Stimulsoft\Result::error("[{$err['type']}] {$err['message']} ({$err['file']}, Line {$err['line']})");
			\Stimulsoft\Response::json($result);
		}
	}

	public function registerErrorHandlers()
	{
		\set_error_handler(array($this, 'errorHandler'));
		\register_shutdown_function(array($this, 'shutdownFunction'));
	}

	/**
	 * Perform the requested action
	 *
	 * @param bool $response if true, will return JSON string
	 * @param bool $exit if true, will echo JSON string and exit
	 *
	 * @return \Stimulsoft\Result if $response is false, otherwise JSON String
	 */
	public function process($response = true, $exit = true)
	{
		$result = $this->innerProcess();

		if ($response) {
			$result = \Stimulsoft\Response::json($result, $exit);
		}

		return $result;
	}

	/**
	 * Create the database connection. Override to return a custom Adapter
	 *
	 * @param \Stimulsoft\Result $result fully populated with parse already called
	 *
	 * @return \Stimulsoft\Result
	 */
	public function getDataAdapter(\Stimulsoft\Result $result)
	{
		$class = __NAMESPACE__ . '\\Adapter\\' . \str_replace(' ', '', $result->object->database);

		if (\class_exists($class)) {
			$dataAdapter = new $class();
			$dataAdapter->parse($result->object->connectionString);

			return \Stimulsoft\Result::success(null, $dataAdapter);
		}

		return \Stimulsoft\Result::error('Unknown database type [' . $request->object->database . ']');
	}

	/**
	 * Execute the passed closure passing it the arguments
	 *
	 * @param \Closure $event closure to run.  Closure can return bool, string (error message) or \Stimulsoft\Result
	 * @param stdclass $args passed to closure
	 *
	 * @return \Stimulsoft\Result
	 */
	private function checkEventResult(\Closure $event, $args)
	{
		// call the closure
		if (isset($event)) {
			$result = $event($args);
		}

		// default to success if no closure
		if (! isset($result)) {
			$result = \Stimulsoft\Result::success();
		}

		if (true === $result) {
			return \Stimulsoft\Result::success();
		}

		if (false === $result) {
			return \Stimulsoft\Result::error();
		}

		if ('string' == \gettype($result)) {
			return \Stimulsoft\Result::error($result);
		}

		if (isset($args)) {
			$result->object = $args;
		}

		return $result;
	}

	private function applyQueryParameters($query, $parameters, $escape)
	{
		$result = '';

		while (false !== \mb_strpos($query, '@')) {
			$result .= \mb_substr($query, 0, \mb_strpos($query, '@'));
			$query = \mb_substr($query, \mb_strpos($query, '@') + 1);

			$parameterName = '';

			while (\strlen($query) > 0) {
				$char = \mb_substr($query, 0, 1);

				if (! \preg_match('/[a-zA-Z0-9_-]/', $char)) {
					break;
				}

				$parameterName .= $char;
				$query = \mb_substr($query, 1);
			}

			$replaced = false;

			foreach ($parameters as $key => $item) {
				if (\strtolower($key) == \strtolower($parameterName)) {
					switch ($item->typeGroup) {
						case 'number':
							$result .= $item->value;

							break;

						case 'datetime':
							$result .= "'" . $item->value . "'";

							break;

						default:
							$result .= "'" . ($escape ? \addcslashes($item->value, "\\\"'") : $item->value) . "'";

							break;
					}

					$replaced = true;
				}
			}

			if (! $replaced) {
				$result .= '@' . $parameterName;
			}
		}

		return $result . $query;
	}

	private function addAddress($param, $settings, $mail)
	{
		$arr = $settings->{$param};

		if (null != $arr && \count($arr) > 0) {
			if ('cc' == $param) {
				$mail->clearCCs();
			} else {
				$mail->clearBCCs();
			}

			foreach ($arr as $value) {
				$name = \mb_strpos($value, ' ') > 0 ? \mb_substr($value, \mb_strpos($value, ' ')) : '';
				$address = \strlen($name) > 0 ? \mb_substr($value, 0, \mb_strpos($value, ' ')) : $value;

				if ('cc' == $param) {
					$mail->addCC($address, $name);
				} else {
					$mail->addBCC($address, $name);
				}
			}
		}
	}

	private function invokePrepareVariables(\Stimulsoft\Request $request)
	{
		$args = new \stdClass();
		$args->sender = $request->sender;

		$args->variables = array();

		if (isset($request->variables)) {
			foreach ($request->variables as $item) {
				$request->variables[$item->name] = $item;
				$variableObject = new \stdClass();
				$variableObject->value = $item->value;
				$variableObject->type = $item->type;

				if ('Range' === \substr($item->type, -5)) {
					$variableObject->value = new \stdClass();
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
				if (! \array_key_exists($key, $request->variables) ||
					$item->value != $request->variables[$key]->value ||
					'Range' === \substr($item->type, -5) && (
						$item->value->from != $request->variables[$key]->value->from ||
						$item->value->to != $request->variables[$key]->value->to
					)) {
					if (! \is_object($item)) {
						$item = (object)$item;
					}
					$item->name = $key;
					$variables[] = $item;
				}
			}

			$result->variables = $variables;
		}

		return $result;
	}

	private function invokeBeginProcessData(\Stimulsoft\Request $request)
	{
		$args = new \stdClass();
		$args->sender = $request->sender;
		$args->command = $request->command;
		$args->database = $request->database;
		$args->connectionString = isset($request->connectionString) ? $request->connectionString : null;
		$args->queryString = isset($request->queryString) ? $request->queryString : null;
		$args->dataSource = isset($request->dataSource) ? $request->dataSource : null;
		$args->connection = isset($request->connection) ? $request->connection : null;

		if (isset($request->queryString, $request->parameters)) {
			$args->parameters = array();

			foreach ($request->parameters as $item) {
				$args->parameters[$item->name] = $item;
				$item->name = null;
			}
		}

		$result = $this->checkEventResult($this->onBeginProcessData, $args);

		if (isset($result->object->queryString, $args->parameters) && \count($args->parameters) > 0) {
			$result->object->queryString = $this->applyQueryParameters($result->object->queryString, $args->parameters, $request->escapeQueryParameters);
		}

		return $result;
	}

	private function invokeEndProcessData(\Stimulsoft\Request $request, \Stimulsoft\Result $result)
	{
		$args = new \stdClass();
		$args->sender = $request->sender;
		$args->command = $request->command;
		$args->database = $request->database;
		$args->dataSource = isset($request->dataSource) ? $request->dataSource : null;
		$args->connection = isset($request->connection) ? $request->connection : null;
		$args->result = $result;

		return $this->checkEventResult($this->onEndProcessData, $args);
	}

	private function invokeCreateReport(\Stimulsoft\Request $request)
	{
		$args = new \stdClass();
		$args->sender = $request->sender;

		return $this->checkEventResult($this->onCreateReport, $args);
	}

	private function invokeOpenReport(\Stimulsoft\Request $request)
	{
		$args = new \stdClass();
		$args->sender = $request->sender;

		return $this->checkEventResult($this->onOpenReport, $args);
	}

	private function invokeSaveReport(\Stimulsoft\Request $request)
	{
		$args = new \stdClass();
		$args->sender = $request->sender;
		$args->report = $request->report;
		$args->reportJson = $request->reportJson;
		$args->fileName = $request->fileName;

		return $this->checkEventResult($this->onSaveReport, $args);
	}

	private function invokeSaveAsReport(\Stimulsoft\Request $request)
	{
		$args = new \stdClass();
		$args->sender = $request->sender;
		$args->report = $request->report;
		$args->reportJson = $request->reportJson;
		$args->fileName = $request->fileName;

		return $this->checkEventResult($this->onSaveAsReport, $args);
	}

	private function invokePrintReport(\Stimulsoft\Request $request)
	{
		$args = new \stdClass();
		$args->sender = $request->sender;
		$args->fileName = $request->fileName;
		$args->printAction = $request->printAction;

		return $this->checkEventResult($this->onPrintReport, $args);
	}

	private function invokeBeginExportReport(\Stimulsoft\Request $request)
	{
		$args = new \stdClass();
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

	private function invokeEndExportReport(\Stimulsoft\Request $request)
	{
		$args = new \stdClass();
		$args->sender = $request->sender;
		$args->format = $request->format;
		$args->formatName = $request->formatName;
		$args->fileName = $request->fileName;
		$args->fileExtension = $this->getFileExtension($request->format);
		$args->data = $request->data;

		return $this->checkEventResult($this->onEndExportReport, $args);
	}

	private function invokeEmailReport(\Stimulsoft\Request $request)
	{
		$settings = new \Stimulsoft\EmailSettings();
		$settings->to = $request->settings->email;
		$settings->subject = $request->settings->subject;
		$settings->message = $request->settings->message;
		$settings->attachmentName = $request->fileName . '.' . $this->getFileExtension($request->format);

		$args = new \stdClass();
		$args->sender = $request->sender;
		$args->settings = $settings;
		$args->format = $request->format;
		$args->formatName = $request->formatName;
		$args->fileName = $request->fileName;
		$args->data = \base64_decode($request->data);

		$result = $this->checkEventResult($this->onEmailReport, $args);

		if (! $result->success) {
			return $result;
		}

		$guid = \substr(\md5(\uniqid() . \mt_rand()), 0, 12);

		if (! \file_exists('tmp')) {
			\mkdir('tmp');
		}
		\file_put_contents('tmp/' . $guid . '.' . $args->fileName, $args->data);

		// Detect auth mode
		$auth = null != $settings->host && null != $settings->login && null != $settings->password;

		$mail = $this->getMailer($settings);

		if ($auth) {
			$mail->IsSMTP();
		}

		try {
			$mail->CharSet = $settings->charset;
			$mail->IsHTML(false);
			$mail->From = $settings->from;
			$mail->FromName = $settings->name;

			// Add Emails list
			$emails = \preg_split('/,|;/', $settings->to);

			foreach ($emails as $settings->to) {
				$mail->AddAddress(\trim($settings->to));
			}

			// Fill email fields
			$mail->Subject = \htmlspecialchars($settings->subject);
			$mail->Body = $settings->message;
			$mail->AddAttachment('tmp/' . $guid . '.' . $args->fileName, $settings->attachmentName);

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
		} catch (\phpmailerException $e) {
			$error = \strip_tags($e->errorMessage());
		} catch (\Exception $e) {
			$error = \strip_tags($e->getMessage());
		}

		\unlink('tmp/' . $guid . '.' . $args->fileName);

		if (isset($error)) {
			return \Stimulsoft\Result::error($error);
		}

		return $result;
	}

	// Private methods

	/**
	 * Process the request
	 *
	 * @return \Stimulsoft\Result
	 */
	private function innerProcess()
	{
		$request = new \Stimulsoft\Request();
		$result = $request->parse();

		if ($result->success) {
			switch ($request->event) {
				case \Stimulsoft\EventType::BeginProcessData:
					$result = $this->invokeBeginProcessData($request);

					if (! $result->success) {
						return $result;
					}
					$queryString = $result->object->queryString;
					$result = $this->getDataAdapter($result);

					if (! $result->success) {
						return $result;
					}
					$connection = $result->object;

					switch ($request->command) {
						case \Stimulsoft\Command::TestConnection:
							$result = $connection->test();

							break;

						case \Stimulsoft\Command::ExecuteQuery:
							$result = $connection->execute($queryString);

							break;
					}
					if (! $result->success) {
						return $result;
					}

					$result = $this->invokeEndProcessData($request, $result);

					if (! $result->success) {
						return $result;
					}

					if (isset($result->object, $result->object->result)) {
						return $result->object->result;
					}

					return $result;

				case \Stimulsoft\EventType::PrepareVariables:
					return $this->invokePrepareVariables($request);

				case \Stimulsoft\EventType::CreateReport:
					return $this->invokeCreateReport($request);

				case \Stimulsoft\EventType::OpenReport:
					return $this->invokeOpenReport($request);

				case \Stimulsoft\EventType::SaveReport:
					return $this->invokeSaveReport($request);

				case \Stimulsoft\EventType::SaveAsReport:
					return $this->invokeSaveAsReport($request);

				case \Stimulsoft\EventType::PrintReport:
					return $this->invokePrintReport($request);

				case \Stimulsoft\EventType::BeginExportReport:
					return $this->invokeBeginExportReport($request);

				case \Stimulsoft\EventType::EndExportReport:
					return $this->invokeEndExportReport($request);

				case \Stimulsoft\EventType::EmailReport:
					return $this->invokeEmailReport($request);
			}

			$result = \Stimulsoft\Result::error('Unknown event [' . $request->event . ']');
		}

		return $result;
	}

	/**
	 * get the file extension give the format
	 *
	 * @param int $format
	 *
	 * @return string
	 */
	private function getFileExtension($format)
	{
		switch ($format) {
			case \Stimulsoft\ExportFormat::Pdf:
				return 'pdf';

			case \Stimulsoft\ExportFormat::Text:
				return 'txt';

			case \Stimulsoft\ExportFormat::Excel2007:
				return 'xlsx';

			case \Stimulsoft\ExportFormat::Word2007:
				return 'docx';

			case \Stimulsoft\ExportFormat::Csv:
				return 'csv';

			case \Stimulsoft\ExportFormat::ImageSvg:
				return 'svg';

			case \Stimulsoft\ExportFormat::Html:
			case \Stimulsoft\ExportFormat::Html5:
				return 'html';

			case \Stimulsoft\ExportFormat::Ods:
				return 'ods';

			case \Stimulsoft\ExportFormat::Odt:
				return 'odt';

			case \Stimulsoft\ExportFormat::Ppt2007:
				return 'pptx';

			case \Stimulsoft\ExportFormat::Document:
				return 'mdc';
		}

		return $format;
	}
}
