<?php

namespace Stimulsoft;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class StiFunctions
{

### String

    public static function startsWith($haystack, $needle): bool
    {
        return !($haystack === null || $needle === null) && substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
    }

    public static function endsWith($haystack, $needle): bool
    {
        return !($haystack === null || $needle === null) && substr_compare($haystack, $needle, -strlen($needle ?? '')) === 0;
    }

    public static function isNullOrEmpty($str): bool
    {
        return strlen($str || '') == 0;
    }

    public static function newGuid($length = 16): string
    {
        return bin2hex(openssl_random_pseudo_bytes($length));
    }

    public static function getJavaScriptValue($value): string
    {
        return $value === null ? 'null' : json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public static function isJavaScriptFunctionName($value): bool
    {
        return preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $value);
    }


### Reflection

    public static function populateObject($class, $object)
    {
        if ($object !== null) {
            $vars = get_object_vars($object);
            if (count($vars) > 0) {
                $default = get_class_vars(get_class($class));
                foreach ($object as $name => $value)
                    if (property_exists($class, $name) && $class->$name == $default[$name])
                        $class->$name = $value;
            }
        }
    }

    public static function getConstants($class, $names = false): array
    {
        try {
            $reflection = new ReflectionClass($class);
            $constants = $reflection->getConstants();
            return $names ? array_flip($constants) : array_values($constants);
        }
        catch (ReflectionException $e) {
            return [];
        }
    }

    public static function getProperties($class, $exclude = []): array
    {
        try {
            $reflection = new ReflectionClass($class);
            $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
            $names = [];
            foreach ($properties as $property) {
                if (!in_array($property->name, $exclude))
                    $names[] = $property->name;
            }

            return $names;
        }
        catch (ReflectionException $e) {
            return [];
        }
    }

    public static function isEnumeration($class, $name): bool
    {
        try {
            $reflection = new ReflectionClass($class);
            $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($properties as $property) {
                if ($property->name == $name) {
                    $doc = $property->getDocComment();
                    return strpos($doc, '[enum]') !== false;
                }
            }
        }
        catch (ReflectionException $e) {
        }

        return false;
    }

    public static function isDashboardsProduct(): bool
    {
        return class_exists('\Stimulsoft\Report\StiDashboard');
    }
}