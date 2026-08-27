<?php

namespace Stimulsoft;

use Stimulsoft\Enums\StiDataType;

class StiResourcesHelper
{
    private static function getDirectory(string $name): ?string
    {
        if (StiFunctions::endsWith($name, '.xml')) return 'localization';
        else if (StiFunctions::endsWith($name, '.js')) return 'scripts';
        return null;
    }

    private static function getFormat(string $name): ?string
    {
        if (StiFunctions::endsWith($name, '.xml')) return StiDataType::XML;
        else if (StiFunctions::endsWith($name, '.js')) return StiDataType::JavaScript;
        return null;
    }

    public static function getFilePath(string $name, ?string $packageDirectory = null): ?string
    {
        $resourceDirectory = self::getDirectory($name);
        if ($resourceDirectory === null)
            return null;

        $packageDirectory = $packageDirectory ?? dirname(__FILE__) . '/../..';
        $resourcePath = "$packageDirectory/$resourceDirectory/$name";
        $path = new StiPath($resourcePath);

        return $path->filePath;
    }

    public static function getResult(string $name, ?string $packageDirectory = null): StiFileResult
    {
        $resourceDirectory = self::getDirectory($name);
        if ($resourceDirectory === null)
            return StiFileResult::getError('Unknown resource format.');

        $filePath = self::getFilePath($name, $packageDirectory);
        if ($filePath === null)
            return StiFileResult::getError("The resource file '$name' was not found.");

        $data = file_get_contents($filePath);
        $dataType = self::getFormat($name);
        return new StiFileResult($data, $dataType);
    }
}