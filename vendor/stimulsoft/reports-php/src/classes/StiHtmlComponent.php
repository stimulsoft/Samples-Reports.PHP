<?php

namespace Stimulsoft;

class StiHtmlComponent
{
    public $id;
    public $isHtmlRendered = false;

    protected function getEventHtml($event, $callback = false, $prevent = false)
    {
        $property = $this->id;
        switch ($this->id) {
            case 'StiReport':
                $property = 'report';
                break;

            case 'StiViewer':
                $property = 'viewer';
                break;

            case 'StiDesigner':
                $property = 'designer';
                break;
        }

        $eventValue = $this->{$event} === true ? '' : 'if (typeof ' . $this->{$event} . ' === "function") ' . $this->{$event} . '(args); ';
        $callbackValue = $callback ? ', callback' : '';
        $preventValue = $prevent ? 'args.preventDefault = true; ' : '';
        return "$property.$event = function (args$callbackValue) { {$preventValue}{$eventValue}Stimulsoft.Helper.process(args$callbackValue); }\n";
    }

    /** Get the HTML representation of the component. */
    public function getHtml()
    {
        return '';
    }

    /** Output of the HTML representation of the component. */
    public function renderHtml()
    {
        echo $this->getHtml();
    }
}