<?php

namespace Stimulsoft;

/**
 * JavaScript helper class
 *
 * **WARNING:** Static functions echo out JavaScript so are not recommend for use in modern PHP code.
 *
 * Recommended usage is to construct an object with your required parameters, then call the getJavaScript method to add the correct JavaScript to your page.
 */
class Helper
{
	private $handlerUrl = '';
	private $licenseUrl = '';
	private $timeout = 30;

	/**
	 * OO interface to for helper that does not echo
	 *
	 * @param string $handlerUrl url that will be called on the server
	 * @param string $licenseUrl url that returns the license
	 * @param int $timeout in seconds
	 */
	public function __construct($handlerUrl = 'handler.php', $licenseUrl = 'stimulsoft/license.php', $timeout = 30)
		{
		$this->handlerUrl = $handlerUrl;
		$this->licenseUrl = $licenseUrl;
		$this->timeout = $timeout;
		}

	/**
	 * Return the JavaScript wihtout tags (as they are HTML, not JavaScript)
	 *
	 * @return string of pure JavaScript
	 */
	public function getJavaScript()
		{
		return <<<JAVASCRIPT
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
						if (args.report && (args.event == 'SaveReport' || args.event == 'SaveAsReport'))
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
				Stimulsoft.Base.StiLicense.loadFromFile("{$this->licenseUrl}");
			}
		}

		Stimulsoft = Stimulsoft || {};
		Stimulsoft.Helper = new StiHelper('{$this->handlerUrl}', {$this->timeout});
		jsHelper = typeof jsHelper !== 'undefined' ? jsHelper : Stimulsoft.Helper;
JAVASCRIPT;
		}

	/**
	 * Example static interface with defaults
	 *
	 * @return \stdClass
	 */
	public static function createOptions()
	{
		$options = new \stdClass();
		$options->handler = 'handler.php';
		$options->timeout = 30;
		$options->licenseUrl = 'stimulsoft/license.php';

		return $options;
	}

	/**
	 * Alternate static interface with defaults that will echo out JavaScript
	 *
	 * @param stdClass $options that must be compatible with createOptions static method
	 *
	 * @return void
	 */
	public static function initialize($options)
	{
		if (! isset($options)) {
			$options = StiHelper::createOptions();
		}
		StiHelper::init($options->handler, $options->timeout, $options->licenseUrl);
	}

	/**
	 * Alternate static interface with defaults that will echo out HTML with JavaScript contained in <script> tags.
	 *
	 * @return void
	 */
	public static function init($handler = 'handler.php', $timeout = 30, $licenseUrl = 'stimulsoft/license.php')
	{
		$helper = new Helper($handler, $licenseUrl, $timeout);
		echo '<script type="text/javascript">';
		echo $helper->getJavaScript();
		echo '</script>';
	}

	/**
	 * Create a handler by echoing out JavaScript
	 *
	 * @return string of JavaScript
	 */
	public static function createHandler()
	{
		echo 'Stimulsoft.Helper.process(arguments[0], arguments[1]);';
	}
}
