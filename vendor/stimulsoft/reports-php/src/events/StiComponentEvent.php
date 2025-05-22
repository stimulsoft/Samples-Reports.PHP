<?php

namespace Stimulsoft\Events;

use Stimulsoft\Enums\StiComponentType;
use Stimulsoft\StiResult;
use Stimulsoft\StiComponent;
use Stimulsoft\StiFunctions;

class StiComponentEvent extends StiEvent
{

### Properties

    private $component;
    public $htmlRendered;


### Helpers

    public function getResult(StiEventArgs $args, $resultClass = null)
    {
        if ($resultClass == null)
            $resultClass = StiResult::class;

        return parent::getResult($args, $resultClass);
    }

    protected function setArgs($args)
    {
        $eventArgs = parent::setArgs($args);
        if (is_a($eventArgs, '\Stimulsoft\Events\StiEventArgs'))
            $eventArgs->sender = $this->component;

        return $eventArgs;
    }


### HTML

    /**
     * Gets the HTML representation of the event.
     * @param bool $callback Adding a callback function.
     * @param bool $prevent Preventing standard client-side processing.
     * @param bool $process Processing event on the server side.
     * @param bool $internal A custom event that is not supported by the JavaScript component.
     */
    public function getHtml(bool $callback = false, bool $prevent = false, bool $process = true, bool $internal = false): string
    {
        if ($this->getLength() == 0 || $this->htmlRendered)
            return '';

        $result = '';
        $componentId = $this->component->id;
        $clientScript = '';
        $eventName = substr($this->name, 2);
        $callback = $callback && $this->hasServerCallbacks();
        $process = $process && $this->hasServerCallbacks();

        // Prepare client-side events
        foreach ($this->callbacks as $callbackName)
            if (is_string($callbackName))
                $clientScript .= StiFunctions::isJavaScriptFunctionName($callbackName)
                    ? "if (typeof $callbackName === \"function\") $callbackName(args); "
                    : "$callbackName ";

        // Prepare args for internal event
        if ($internal) {
            $componentType = $this->component->getComponentType();
            $report = property_exists($this->component, "report") ? $this->component->report : null;
            $reportId = $componentType == StiComponentType::Report
                ? $componentId : ($report != null ? "$componentId.{$report->id}" : "null");

            $result .= "var args = {event: \"$eventName\", sender: \"$componentType\", report: $reportId, preventDefault: false};\n";
            if (!StiFunctions::isNullOrEmpty($clientScript))
                $result .= "$clientScript\n";
        }

        // For an internal event, the args and the callback function must have a unique name
        $callbackName = $internal ? $this->component->id . $eventName . 'Callback' : 'callback';
        $argsArgument = $internal ? "args$eventName" : "args";

        // Prepare event parameters
        $callbackArgument = $callback ? ", $callbackName" : '';
        $preventValue = $prevent ? 'args.preventDefault = true; ' : '';
        $processValue = $process ? "Stimulsoft.handler.process($argsArgument$callbackArgument); " : ($callback ? "$callbackName(); " : '');

        // For an internal event, the function is called in the next JavaScript frame (a zero timeout is used)
        $internalValue = $callback ? "let $argsArgument = args;\nlet $callbackName = null;\nsetTimeout(function () { " . $preventValue . $processValue . "});\n" : "";
        $eventValue = "$componentId.$this->name = function (args$callbackArgument) { " . $preventValue . $clientScript . $processValue . "};\n";
        $result .= $internal ? $internalValue : $eventValue;

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