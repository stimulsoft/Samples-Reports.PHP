<?php

namespace Stimulsoft\Events;

use Stimulsoft\Report\Enums\StiVariableType;
use Stimulsoft\Report\StiVariable;
use Stimulsoft\Report\StiVariableRange;

class StiVariablesEventArgs extends StiEventArgs
{

### Properties

    /** @var array A set of Request from User variables (if they are present in the current report). */
    public $variables;


### Helpers

    protected function setProperty($name, $value)
    {
        parent::setProperty($name, $value);

        if ($name == 'variables') {
            $this->variables = [];
            foreach ($value as $item) {
                $variable = new StiVariable($item->name);
                $variable->value = $item->value;
                $variable->type = $item->type;

                if (StiVariableType::isRange($item->type))
                    $variable->value = new StiVariableRange($item->value->from, $item->value->to);

                $this->variables[$item->name] = $variable;
            }
        }
    }
}