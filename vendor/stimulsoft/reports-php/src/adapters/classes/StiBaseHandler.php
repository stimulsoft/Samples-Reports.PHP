<?php

namespace Stimulsoft;

use Exception;
use ReflectionClass;
use Stimulsoft\Adapters\StiDataAdapter;
use Stimulsoft\Adapters\StiMongoDbAdapter;
use Stimulsoft\Enums\StiDatabaseType;
use Stimulsoft\Enums\StiDataCommand;
use Stimulsoft\Enums\StiBaseEventType;
use Stimulsoft\Events\StiEvent;
use Stimulsoft\Events\StiDataEventArgs;

/**
 * Event handler for all requests from the report generator. Processes the incoming request, communicates with data adapters,
 * prepares parameters and triggers events, and performs all necessary actions. After this, the event handler prepares
 * a response for the web server.
 */
class StiBaseHandler
{

### Properties

    public static $legacyMode = false;

    /** @var string Current version of the event handler. */
    public $version = '2025.1.2';

    /** @var bool Enables checking for client-side and server-side data adapter versions to match. */
    public $checkDataAdaptersVersion = true;

    /** @var string */
    public $origin;

    /** @var array */
    public $query;

    /** @var string */
    public $body;

    /** @var string */
    public $error;

    /** @var StiBaseRequest */
    public $request;

    /** @var StiDataAdapter */
    public $dataAdapter;

    /** @var string The URL for event handler requests. If not specified, the current URL is used. */
    public $url;

    /** @var bool Enables automatic passing of GET parameters from the current URL to event handler requests. */
    public $passQueryParameters = false;


### Events

    /** @var StiEvent The event is invoked before connecting to the database after all parameters have been received. */
    public $onDatabaseConnect;

    /** @var StiEvent The event is invoked before data request, which needed to render a report. */
    public $onBeginProcessData;

    /** @var StiEvent The event is invoked after loading data before rendering a report. */
    public $onEndProcessData;


### Error handlers

    public function stiErrorHandler($errNo, $errStr, $errFile, $errLine)
    {
        $class = new ReflectionClass($this);
        $message = "[$errNo] {$class->getShortName()} ($errFile, Line $errLine) - $errStr";
        $result = StiBaseResult::getError($message);
        $result->handlerVersion = $this->version;
        $response = new StiBaseResponse($this, $result);
        $response->printData();
    }

    public function stiShutdownFunction()
    {
        $err = error_get_last();
        if ($err != null && (($err['type'] & E_COMPILE_ERROR) || ($err['type'] & E_ERROR) || ($err['type'] & E_CORE_ERROR) || ($err['type'] & E_RECOVERABLE_ERROR))) {
            $class = new ReflectionClass($this);
            $message = "[{$err['type']}] {$class->getShortName()} ({$err['file']}, Line {$err['line']}) - {$err['message']}";
            $result = StiBaseResult::getError($message);
            $response = new StiBaseResponse($this, $result);
            $response->printData();
        }
    }

    public function registerErrorHandlers()
    {
        set_error_handler(array($this, 'stiErrorHandler'));
        register_shutdown_function(array($this, 'stiShutdownFunction'));
        error_reporting(0);
    }


### Legacy

    public static function enableLegacyMode()
    {
        StiBaseHandler::$legacyMode = true;

        class_alias('Stimulsoft\Enums\StiDatabaseType', 'Stimulsoft\StiDatabaseType');
        class_alias('Stimulsoft\Enums\StiDataCommand', 'Stimulsoft\StiDataCommand');
    }


### Helpers

    private function setQuery($query)
    {
        $this->query = $query != null ? $query : $_GET;
    }

    private function setBody($body)
    {
        $this->body = $body != null ? $body : file_get_contents('php://input');
    }

    protected function createRequest()
    {
        return new StiBaseRequest();
    }

    protected function checkEvent(): bool
    {
        $values = StiBaseEventType::getValues();
        return $this->request->event == null || in_array($this->request->event, $values);
    }

    protected function checkCommand(): bool
    {
        $values = StiDataCommand::getValues();
        return in_array($this->request->command, $values);
    }

    protected function updateEvents()
    {
        $this->updateEvent('onDatabaseConnect');
        $this->updateEvent('onBeginProcessData');
        $this->updateEvent('onEndProcessData');
    }

    protected function updateEvent(string $eventName)
    {
        if ($this->$eventName instanceof StiEvent) return;

        $callback = is_callable($this->$eventName) || is_string($this->$eventName) || is_bool($this->$eventName) ? $this->$eventName : null;
        $this->$eventName = new StiEvent($this, $eventName);
        if ($callback !== null) $this->$eventName->append($callback);
    }

    private function processParameters()
    {
        $parameters = [];
        if ($this->request->parameters !== null && $this->request->queryString !== null && strlen($this->request->queryString) > 0) {
            foreach ($this->request->parameters as $item) {
                $name = mb_strpos($item->name, '@') === 0 || mb_strpos($item->name, ':') === 0 ? mb_substr($item->name, 1) : $item->name;
                $parameters[$name] = new StiParameter($name, $item->type, $item->typeName, $item->typeGroup, $item->size, $item->value);
            }
        }

        $this->request->parameters = $parameters;
    }

    public function getUrl()
    {
        $url = $this->url ?? '';
        if ($this->passQueryParameters) {
            foreach ($_GET as $key => $value)
                if (strpos($url, $key) === false) {
                    $url .= strpos($url, '?') === false ? '?' : '&';
                    $url .= "$key=" . rawurlencode($value);
                }
        }

        return $url;
    }


### Request

    /**
     * Processing an HTTP request from the client side of the component. If successful, it is necessary to return a response
     * with the processing result, which can be obtained using the 'getResponse()' function.
     * @param string $query The GET query string if no framework request is specified.
     * @param string $body The POST form data if no framework request is specified.
     * @return bool True if the request was processed successfully.
     */
    public function processRequest(string $query = null, string $body = null): bool
    {
        $this->error = null;

        try {
            $this->setQuery($query);
            $this->setBody($body);
        }
        catch (Exception $e) {
            $this->error = 'Request: ' . $e->getMessage();
            return false;
        }

        $this->request->process($this->query, $this->body);
        if (strlen($this->request->error ?? '') > 0) {
            $this->error = $this->request->error;
            return false;
        }

        if ($this->checkEvent() == false) {
            $this->error = "Unknown event: {$this->request->event}";
            return false;
        }

        if ($this->checkCommand() == false) {
            $command = $this->request->command === null ? 'null' : $this->request->command;
            $this->error = "Unknown command: $command";
            return false;
        }

        return true;
    }

    /**
     * Processing an HTTP request from the client side of the component. After processing, it immediately prints the result and exits.
     * @param bool $printAll Printing of all processing results, or only successful ones.
     */
    public function process(bool $printAll = false)
    {
        $result = $this->processRequest();
        if ($result || $printAll)
            $this->getResponse()->printData();
    }


### Results

    private function getSupportedDataAdaptersResult(): StiBaseResult
    {
        $result = StiBaseResult::getSuccess();
        $result->types = StiDatabaseType::getValues();
        $result->handlerVersion = $this->version;
        return $result;
    }

    private function getDataAdapterResult()
    {
        $this->updateEvents();

        $args = new StiDataEventArgs($this->request);
        $this->onBeginProcessData->call($args);

        $this->dataAdapter = StiDataAdapter::getDataAdapter($args->database, $args->connectionString);
        if ($this->dataAdapter == null)
            return StiBaseResult::getError("Unknown database type: $args->database");

        $this->dataAdapter->handler = $this;

        if ($this->request->command == StiDataCommand::RetrieveSchema) {
            $args->result = $this->dataAdapter->executeQuery($args->dataSource, $args->maxDataRows);
            $this->onEndProcessData->call($args);
            return $args->result;
        }

        if ($this->request->command == StiDataCommand::Execute || $this->request->command == StiDataCommand::ExecuteQuery) {
            if ($this->dataAdapter instanceof StiMongoDbAdapter)
                $args->queryString = $args->dataSource;

            if ($this->request->command == StiDataCommand::Execute)
                $args->queryString = $this->dataAdapter->makeQuery($args->queryString, $args->parameters);

            if (count($this->request->parameters) > 0)
                $args->queryString = StiDataAdapter::applyQueryParameters($args->queryString, $args->parameters, $this->request->escapeQueryParameters);

            $args->result = $this->dataAdapter->executeQuery($args->queryString, $args->maxDataRows);
            $this->onEndProcessData->call($args);
            return $args->result;
        }

        return $this->dataAdapter->test();
    }

    /**
     * Returns the result of processing a request from the client side. The response object will contain the data for the response,
     * as well as their MIME type, Content-Type, and other useful information to create a web server response.
     */
    public function getResponse()
    {
        return new StiBaseResponse($this);
    }

    /**
     * Returns the result of processing a request from the client side. The result object will contain a collection of data,
     * message about the result of the command execution, and other technical information.
     */
    public function getResult()
    {
        if ($this->error !== null && strlen($this->error) > 0) {
            $result = StiBaseResult::getError($this->error);
            $result->handlerVersion = $this->version;
            return $result;
        }

        if ($this->request->command == StiDataCommand::GetSupportedAdapters)
            return $this->getSupportedDataAdaptersResult();

        $this->processParameters();
        $result = $this->getDataAdapterResult();
        $result->handlerVersion = $this->version;
        return $result;
    }


### Constructor

    public function __construct($url = null, $registerErrorHandlers = true)
    {
        if (StiBaseHandler::$legacyMode && is_bool($url)) {
            $registerErrorHandlers = $url;
            $url = null;
        }

        $this->request = $this->createRequest();

        if ($registerErrorHandlers)
            $this->registerErrorHandlers();

        $this->url = $url;
        $this->updateEvents();
    }
}
