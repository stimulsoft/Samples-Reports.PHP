<?php

namespace Stimulsoft\Report;

use Stimulsoft\StiElement;

class StiDictionary extends StiElement
{
    /** @var StiReport */
    public $report;

    /** @var array */
    public $variables;


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

    public function __construct(StiReport $report)
    {
        $this->report = $report;
        $this->variables = [];
    }
}