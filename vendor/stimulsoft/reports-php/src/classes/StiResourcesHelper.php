<?php

namespace Stimulsoft;

use Stimulsoft\Enums\StiDataType;

class StiResourcesHelper
{
    private static function getDirectory(string $name)
    {
        if (StiFunctions::endsWith($name, '.xml')) return 'localization';
        else if (StiFunctions::endsWith($name, '.js')) return 'scripts';
        return null;
    }

    private static function getFormat(string $name)
    {
        if (StiFunctions::endsWith($name, '.xml')) return StiDataType::XML;
        else if (StiFunctions::endsWith($name, '.js')) return StiDataType::JavaScript;
        return null;
    }

    public static function getResult(string $name): StiFileResult
    {
        $resourceDirectory = self::getDirectory($name);
        if ($resourceDirectory === null)
            return StiFileResult::getError('Unknown resource format.');

        $fileDirectory = dirname(__FILE__);
        $resourcePath = "$fileDirectory/../../$resourceDirectory/$name";
        $path = new StiPath($resourcePath);
        if ($path->filePath !== null) {
            $data = file_get_contents($path->filePath);
            $dataType = self::getFormat($name);
            return new StiFileResult($data, $dataType);
        }

        return StiFileResult::getError("The resource file '$name' was not found.");
    }
}