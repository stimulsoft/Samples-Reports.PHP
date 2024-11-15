<?php

namespace Stimulsoft\Report;

use Stimulsoft\Enums\Types;
use Stimulsoft\StiElement;

class StiFunctions extends StiElement
{

### Fields

    private static $functions = [];


### Methods

    /**
     * Adds the specified JavaScript function to the collection for use in the report generator.
     * @param string $category The name of the category in the designer's data dictionary.
     * @param string $groupFunctionName The name of a function group in the designer's data dictionary.
     * @param string $functionName The name of the function.
     * @param string $description The description of the function.
     * @param string $typeOfFunction The kind of this function.
     * @param string|Types $returnType [enum] The return type of the function.
     * @param string $returnDescription The description of the function's return result.
     * @param string[]|Types[]|null $argumentTypes [enum] The array of function parameter types.
     * @param string[]|null $argumentNames The array of function parameter names.
     * @param string[]|null $argumentDescriptions The array of function parameter descriptions.
     * @param string|null $jsFunction The name of an existing JavaScript function, or the JavaScript function itself.
     */
    public static function addFunction(string $category, string $groupFunctionName, string $functionName, string $description, string $typeOfFunction,
                                       string $returnType, string $returnDescription = "", array $argumentTypes = null, array $argumentNames = null,
                                       array $argumentDescriptions = null, string $jsFunction = null)
    {
        self::$functions[] = new StiCustomFunction(
            $category, $groupFunctionName, $functionName, $description, $typeOfFunction, $returnType,
            $returnDescription, $argumentTypes, $argumentNames, $argumentDescriptions, $jsFunction);
    }


### HTML

    public function getHtml(): string
    {
        $result = '';

        /** @var StiCustomFunction $function */
        foreach (self::$functions as $function) {
            $result .= $function->getHtml();
        }

        return $result . parent::getHtml();
    }
}