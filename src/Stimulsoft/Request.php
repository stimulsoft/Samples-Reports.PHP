<?php

namespace Stimulsoft;

class Request
{
	public $sender = null;

	public $event = null;

	public function parse()
	{
		$input = \file_get_contents('php://input');

		if (\strlen($input) > 0 && '{' != \mb_substr($input, 0, 1)) {
			$input = \base64_decode(\str_rot13($input));
		}

		$obj = \json_decode($input);

		if (null == $obj) {
			$message = 'JSON parser error #' . \json_last_error();

			if (\function_exists('json_last_error_msg')) {
				$message .= ' (' . \json_last_error_msg() . ')';
			}

			return \Stimulsoft\Result::error($message);
		}

		$parameterNames = array(
			'sender', 'event', 'command', 'connectionString', 'queryString', 'database', 'dataSource', 'connection', 'timeout', 'data',
			'fileName', 'action', 'printAction', 'format', 'formatName', 'settings', 'variables', 'parameters', 'escapeQueryParameters'
		);

		foreach ($parameterNames as $name) {
			if (isset($obj->{$name})) {
				$this->{$name} = $obj->{$name};
			}
		}

		if (! isset($obj->event) && isset($obj->command) && (\Stimulsoft\Command::TestConnection == $obj->command || \Stimulsoft\Command::ExecuteQuery)) {
			$this->event = \Stimulsoft\EventType::BeginProcessData;
		}

		if (isset($obj->report)) {
			$this->report = $obj->report;

			if (\defined('JSON_UNESCAPED_SLASHES')) {
				$this->reportJson = \json_encode($this->report, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
			} else {
				// for PHP 5.3
				$this->reportJson = \str_replace('\/', '/', \json_encode($this->report));
				$this->reportJson = \preg_replace_callback('/\\\\u(\w{4})/', function($matches) {
					return \html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
				}, $this->reportJson);
			}
		}

		return \Stimulsoft\Result::success(null, $this);
	}
}
