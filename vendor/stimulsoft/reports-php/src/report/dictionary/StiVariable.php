<?php

namespace Stimulsoft\Report;

use Stimulsoft\Report\Enums\StiVariableType;
use Stimulsoft\StiElement;

class StiVariable extends StiElement
{

### Properties

    /** @var string The name of the variable. */
    public $name;

    /** @var StiVariableType|string The type of the variable. Is equal to one of the values of the StiVariableType enumeration. */
    public $type;

    /** @var object|string|int|bool The value of the variable. The type of object depends on the type of variable. */
    public $value;


### HTML

    public function getHtml(): string
    {
        $id = $this->id !== null ? $this->id : $this->name;
        $result =
            "let $id = new Stimulsoft.Report.Dictionary.StiVariable" .
            "('', '{$this->name}', '{$this->name}', '', Stimulsoft.System.{$this->type}, '{$this->value}');\n";

        return $result . parent::getHtml();
    }


### Constructor

    /**
     * StiVariable constructor.
     * @param string $name The name of the variable.
     * @param StiVariableType|string $type The type of the variable.
     * @param object|string|int|bool $value The value of the variable. The type of value object depends on the type of variable.
     */
    public function __construct(string $name, $type = StiVariableType::String, string $value = '')
    {
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
    }
}