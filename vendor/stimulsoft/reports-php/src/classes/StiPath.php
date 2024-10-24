<?php

namespace Stimulsoft;

class StiPath
{

### Fields

    public $filePath;
    public $directoryPath;
    public $fileName;
    public $fileNameOnly;
    public $fileExtension;


### Helpers

    private static function normalizePath($path): string
    {
        $path = str_replace('\\', '/', $path != null ? $path : '');
        $result = preg_replace(['~/{2,}~', '~/(\./)+~', '~([^/\.]+/(?R)*\.{2,}/)~', '~\.\./~'], ['/', '/', '', ''], $path);
        $normalized = explode('?', $result)[0];
        return str_replace('/', DIRECTORY_SEPARATOR, $normalized);
    }

    private static function getMissingFileName($filePath): string
    {
        $filePath = StiPath::normalizePath($filePath);
        return basename($filePath);
    }

    private static function getRealFilePath($filePath)
    {
        $filePath = StiPath::normalizePath($filePath);
        if (is_file($filePath))
            return $filePath;

        $filePath = getcwd() . DIRECTORY_SEPARATOR . $filePath;
        if (is_file($filePath))
            return $filePath;

        return null;
    }

    private static function getRealDirectoryPath($directoryPath)
    {
        $filePath = StiPath::normalizePath($directoryPath);

        $directoryPath = $filePath;
        if (is_dir($directoryPath))
            return $directoryPath;

        $directoryPath = getcwd() . DIRECTORY_SEPARATOR . $directoryPath;
        if (is_dir($directoryPath))
            return $directoryPath;

        $directoryPath = dirname($filePath);
        if (is_dir($directoryPath))
            return $directoryPath;

        $directoryPath = getcwd() . DIRECTORY_SEPARATOR . $directoryPath;
        if (is_dir($directoryPath))
            return $directoryPath;

        return null;
    }


### Constructor

    public function __construct($filePath)
    {
        $this->filePath = self::getRealFilePath($filePath);
        $this->directoryPath = self::getRealDirectoryPath($filePath);

        $this->fileName = $this->filePath !== null ? basename($this->filePath) : self::getMissingFileName($filePath);
        if ($this->filePath === null && StiFunctions::endsWith($this->directoryPath, $this->fileName))
            $this->fileName = null;

        if ($this->fileName !== null) {
            $pathInfo = pathinfo($this->fileName);
            $this->fileNameOnly = strlen($pathInfo['filename'] ?? '') > 0 ? $pathInfo['filename'] : null;
            $this->fileExtension = strlen($pathInfo['extension'] ?? '') > 0 ? $pathInfo['extension'] : null;
        }
    }
}