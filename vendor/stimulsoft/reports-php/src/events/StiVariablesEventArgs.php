<?php

namespace Stimulsoft;

class StiVariablesEventArgs extends StiEventArgs
{
    /** @var array A set of Request from User variables (if they are present in the current report). */
    public $variables;
}