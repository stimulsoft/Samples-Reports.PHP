<?php

namespace Stimulsoft\Report;

class StiVariableRange
{

### Properties

    public $from;
    public $to;


### Constructor

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }
}