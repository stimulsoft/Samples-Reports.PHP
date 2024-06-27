<?php

namespace Stimulsoft\Events;

use Stimulsoft\StiBaseHandler;

class StiEvent
{

### Fields

    /** @var StiBaseHandler */
    protected $handler = null;


### Properties

    public $name = null;
    public $callbacks = null;


### Helpers

    protected function setArgs(...$args)
    {
        $eventArgs = count($args) > 0 ? $args[0] : null;
        if (is_a($eventArgs, '\Stimulsoft\Events\StiEventArgs')) {
            $eventArgs->event = substr($this->name, 2);
            $eventArgs->sender = $this->handler;
            return $eventArgs;
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

    /**
     * Calls all added functions passing the required arguments.
     */
    public function call(...$args)
    {
        foreach ($this->callbacks as $callback) {
            if (is_callable($callback)) {
                $this->setArgs(...$args);
                $result = $callback(...$args);
                if ($result != null)
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