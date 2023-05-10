<?php

namespace Stimulsoft\Report;

use Stimulsoft\StiHtmlComponent;

class StiVariable extends StiHtmlComponent
{
    /** @var string The name of the variable. */
    public $name;

    /** @var StiVariableType The type of the variable. Is equal to one of the values of the StiVariableType enumeration. */
    public $type;

    /** @var object The value of the variable. The type of object depends on the type of variable. */
    public $value;


    /** Get the HTML representation of the component. */
    public function getHtml()
    {
        $result =
            "let $this->id = new Stimulsoft.Report.Dictionary.StiVariable".
            "('', '{$this->name}', '{$this->name}', '', Stimulsoft.System.{$this->type}, '{$this->value}');\n";

        $this->isHtmlRendered = true;
        return $result;
    }

    public function __construct($name = '', $type = 'String', $value = '')
    {
        $this->name = !is_null($name) && strlen($name) > 0 ? $name : 'variable';
        $this->type = $type;
        $this->value = $value;

        $this->id = $this->name;
    }
}