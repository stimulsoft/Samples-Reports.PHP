<?php

namespace Stimulsoft\Report;

use Stimulsoft\StiElement;
use Stimulsoft\StiFunctions;

class StiCustomFunction extends StiElement
{

### Fields

    private $category;
    private $groupFunctionName;
    private $functionName;
    private $description;
    private $typeOfFunction;
    private $returnType;
    private $returnDescription;
    private $argumentTypes;
    private $argumentNames;
    private $argumentDescriptions;
    private $jsFunction;


### Methods

    private function getJavaScriptTypes($value): string
    {
        if (is_string($value))
            return $value;

        if (is_array($value)) {
            $result = '';
            foreach ($value as $type)
                $result .= strlen($result) > 0 ? ", $type" : $type;

            return "[$result]";
        }

        return 'null';
    }


### HTML

    public function getHtml(): string
    {
        $result = '';

        $category = StiFunctions::getJavaScriptValue($this->category);
        $groupFunctionName = StiFunctions::getJavaScriptValue($this->groupFunctionName);
        $functionName = StiFunctions::getJavaScriptValue($this->functionName);
        $description = StiFunctions::getJavaScriptValue($this->description);
        $typeOfFunction = StiFunctions::getJavaScriptValue($this->typeOfFunction);
        $returnType = $this->getJavaScriptTypes($this->returnType);
        $returnDescription = StiFunctions::getJavaScriptValue($this->returnDescription);
        $argumentTypes = $this->getJavaScriptTypes($this->argumentTypes);
        $argumentNames = StiFunctions::getJavaScriptValue($this->argumentNames);
        $argumentDescriptions = StiFunctions::getJavaScriptValue($this->argumentDescriptions);

        $result .= "Stimulsoft.Report.Dictionary.StiFunctions.addFunction($category, $groupFunctionName, $functionName, $description, $typeOfFunction, "
            . "$returnType, $returnDescription, $argumentTypes, $argumentNames, $argumentDescriptions, $this->jsFunction);\n";

        return $result . parent::getHtml();
    }


### Constructor

    public function __construct(string $category, string $groupFunctionName, string $functionName, string $description, string $typeOfFunction, string $returnType,
                                string $returnDescription, array $argumentTypes, array $argumentNames, array $argumentDescriptions, string $jsFunction)
    {
        $this->category = $category;
        $this->groupFunctionName = $groupFunctionName;
        $this->functionName = $functionName;
        $this->description = $description;
        $this->typeOfFunction = $typeOfFunction;
        $this->returnType = $returnType;
        $this->returnDescription = $returnDescription;
        $this->argumentTypes = $argumentTypes;
        $this->argumentNames = $argumentNames;
        $this->argumentDescriptions = $argumentDescriptions;
        $this->jsFunction = $jsFunction;
    }
}