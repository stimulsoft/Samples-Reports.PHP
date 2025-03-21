<?php

namespace Stimulsoft\Events;

use Stimulsoft\StiBaseHandler;
use Stimulsoft\StiBaseResult;
use Stimulsoft\StiDataResult;
use Stimulsoft\StiFunctions;

class StiEvent
{

### Fields

    /** @var StiBaseHandler */
    protected $handler = null;


### Properties

    public $name = null;
    public $callbacks = null;


### Helpers

    public function getResult(StiEventArgs $args, $resultClass = null)
    {
        if ($resultClass == null)
            $resultClass = StiBaseResult::class;

        if ($this->getLength() > 0) {
            $result = $this->call($args);

            if ($result === null || $result === true)
                return $resultClass::getSuccess();

            if ($result === false)
                return $resultClass::getError("An error occurred while processing the '{$this->name}' event.");

            if ($result instanceof StiBaseResult)
                return $result;

            return $resultClass::getSuccess(strval($result));
        }

        return null;
    }

    protected function setArgs($args)
    {
        if (is_a($args, '\Stimulsoft\Events\StiEventArgs')) {
            $args->event = substr($this->name, 2);
            $args->sender = $this->handler;
            return $args;
        }

        return null;
    }

    /**
     * Adds a PHP function or JavaScript function to the event handler that will be called when the event occurs.
     * @param callable|string $callback The PHP function to call, or the name of the JavaScript function.
     */
    public function append($callback)
    {
        if (!in_array($callback, $this->callbacks, true))
            $this->callbacks[] = $callback;
    }

    /**
     * Removes a PHP function or JavaScript function from the event handler.
     * @param callable|string $callback The PHP function to call, or the name of the JavaScript function that was added.
     */
    public function remove($callback)
    {
        $index = array_search($callback, $this->callbacks, true);
        if ($index !== false)
            array_splice($this->callbacks, $index, 1);
    }

    /**
     * Returns the total number of added event functions.
     */
    public function getLength(): int
    {
        return count($this->callbacks);
    }

    public function hasServerCallbacks(): bool
    {
        foreach ($this->callbacks as $callback) {
            if (is_callable($callback) || $callback === true)
                return true;
        }

        return false;
    }

    public function hasClientCallbacks(): bool
    {
        foreach ($this->callbacks as $callback) {
            if (is_string($callback))
                return true;
        }

        return false;
    }

    /**
     * Calls all added functions passing the required arguments.
     */
    public function call($args)
    {
        foreach ($this->callbacks as $callback) {
            if (is_callable($callback)) {
                $this->setArgs($args);
                $result = $callback($args);
                if ($result !== null)
                    return $result;
            }
        }

        return null;
    }


### Constructor

    public function __construct($handler, string $name)
    {
        $this->callbacks = [];
        $this->handler = $handler;
        $this->name = $name;
    }
}