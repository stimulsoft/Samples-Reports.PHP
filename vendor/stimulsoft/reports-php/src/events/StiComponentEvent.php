<?php

namespace Stimulsoft\Events;

use Stimulsoft\StiComponent;

class StiComponentEvent extends StiEvent
{

### Properties

    private $component;
    public $htmlRendered;


### Helpers

    protected function setArgs(...$args)
    {
        $eventArgs = parent::setArgs(...$args);
        if (is_a($eventArgs, '\Stimulsoft\Events\StiEventArgs'))
            $eventArgs->sender = $this->component;

        return $eventArgs;
    }


### HTML

    /**
     * Gets the HTML representation of the event.
     */
    public function getHtml($callback = false, $prevent = false, $process = true, $internal = false): string
    {
        if ($this->getLength() == 0 || $this->htmlRendered)
            return '';

        $componentId = $this->component->id;
        $eventValue = '';
        foreach ($this->callbacks as $callbackName)
            if (is_string($callbackName))
                $eventValue .= preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $callbackName)
                    ? "if (typeof $callbackName === \"function\") $callbackName(args); "
                    : $callbackName;

        if ($internal) {
            $objectArgsName = substr($this->name, 2);
            $componentType = $this->component->getComponentType();
            $eventArgs = "let args = {event: \"$objectArgsName\", sender: \"$componentType\", report: $componentId}";
            return "$eventArgs\n$eventValue\n";
        }

        $callbackValue = $callback ? ', callback' : '';
        $preventValue = $prevent ? 'args.preventDefault = true; ' : '';
        $processValue = $process ? "Stimulsoft.handler.process(args$callbackValue); " : ($callback ? 'callback(); ' : '');
        $result = "$componentId.$this->name = function (args$callbackValue) { $preventValue$eventValue$processValue }\n";

        $this->htmlRendered = true;
        return $result;
    }


### Constructor

    public function __construct(StiComponent $component, string $name)
    {
        parent::__construct($component->handler, $name);
        $this->component = $component;
    }
}