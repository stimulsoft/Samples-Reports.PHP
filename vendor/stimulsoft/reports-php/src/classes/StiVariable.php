<?php

namespace Stimulsoft;

class StiVariable
{
    /** @var string The type of the variable. Is equal to one of the values of the StiVariableType enumeration. */
    public $type;

    /** @var object The value of the variable. The type of object depends on the type of variable. */
    public $value;
}