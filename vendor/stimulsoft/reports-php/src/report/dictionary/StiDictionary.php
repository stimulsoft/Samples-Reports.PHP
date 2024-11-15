<?php

namespace Stimulsoft\Report;

use Stimulsoft\StiElement;

class StiDictionary extends StiElement
{

### Properties

    /** @var StiReport */
    public $report;

    /** @var array */
    public $variables;


### HTML

    public function getHtml(): string
    {
        $result = '';

        /** @var StiVariable $variable */
        foreach ($this->variables as $variable) {
            $result .= $variable->getHtml();
            $result .= "{$this->report->id}.dictionary.variables.add({$variable->id});\n";
        }

        return parent::getHtml() . $result;
    }


### Constructor

    public function __construct(StiReport $report)
    {
        $this->report = $report;
        $this->variables = [];
    }
}