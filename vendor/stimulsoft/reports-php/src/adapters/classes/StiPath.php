<?php

namespace Stimulsoft;

class StiPath
{

### Fields

    /** @var string|null The full path to the file, if the file exists. */
    public $filePath;

    /** @var string|null The full path to the file directory, if the file exists. */
    public $directoryPath;

    /** @var string|null The file name. */
    public $fileName;

    /** @var string|null The file name without the extension. */
    public $fileNameOnly;

    /** @var string|null The file extension. */
    public $fileExtension;

    /** @var string|null The URL to the file, if the file exists (code 200 was received when requesting the file). */
    public $fileUrl;


### Helpers

    public static function getVendorPath()
    {
        return realpath(__DIR__ . "/../../../..");
    }

    public static function normalize($path, $checkFileNames = true): string
    {
        $result = str_replace('\\', '/', $path ?? '');
        $result = $checkFileNames ? preg_replace(['~/{2,}~', '~/(\./)+~', '~([^/\.]+/(?R)*\.{2,}/)~', '~\.\./~'], ['/', '/', '', ''], $result) : $result;
        $normalized = explode('?', $result)[0];
        return str_replace('/', DIRECTORY_SEPARATOR, $normalized);
    }

    private static function isUrl($path): bool
    {
        return StiFunctions::startsWith($path, "http://") || StiFunctions::startsWith($path, "https://");
    }

    private static function getMissingFileName($filePath, $checkFileNames): string
    {
        $filePath = StiPath::normalize($filePath, $checkFileNames);
        return basename($filePath);
    }

    private static function getRealFilePath($filePath, $checkFileNames): ?string
    {
        if (StiPath::isUrl($filePath)) {
            $headers = get_headers($filePath);
            return stripos($headers[0],"200 OK") ? $filePath : null;
        }

        $filePath = StiPath::normalize($filePath, $checkFileNames);
        if (is_file($filePath))
            return $filePath;

        $filePath = StiPath::normalize(getcwd() . '/' . $filePath, $checkFileNames);
        if (is_file($filePath))
            return $filePath;

        return null;
    }

    private static function getRealDirectoryPath($directoryPath, $checkFileNames): ?string
    {
        if (StiPath::isUrl($directoryPath))
            return null;

        $filePath = StiPath::normalize($directoryPath, $checkFileNames);

        $directoryPath = $filePath;
        if (is_dir($directoryPath))
            return $directoryPath;

        $directoryPath = StiPath::normalize(getcwd() . '/' . $directoryPath, $checkFileNames);
        if (is_dir($directoryPath))
            return $directoryPath;

        $directoryPath = dirname($filePath);
        if (is_dir($directoryPath))
            return $directoryPath;

        $directoryPath = StiPath::normalize(getcwd() . '/' . $directoryPath, $checkFileNames);
        if (is_dir($directoryPath))
            return $directoryPath;

        return null;
    }


### Constructor

    public function __construct(?string $filePath, bool $checkFileNames = true)
    {
        $this->filePath = self::getRealFilePath($filePath, $checkFileNames);
        $this->directoryPath = self::getRealDirectoryPath($filePath, $checkFileNames);
        if ($this->filePath !== null && StiPath::isUrl($filePath))
            $this->fileUrl = $this->filePath;

        $this->fileName = $this->filePath !== null ? basename($this->filePath) : self::getMissingFileName($filePath, $checkFileNames);
        if ($this->filePath === null && StiFunctions::endsWith($this->directoryPath, $this->fileName))
            $this->fileName = null;

        if ($this->fileName !== null) {
            $pathInfo = pathinfo($this->fileName);
            $this->fileNameOnly = strlen($pathInfo['filename'] ?? '') > 0 ? $pathInfo['filename'] : null;
            $this->fileExtension = strlen($pathInfo['extension'] ?? '') > 0 ? $pathInfo['extension'] : null;
        }
    }
}