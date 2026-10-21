<?php

namespace Stimulsoft\Adapters;

use Exception;
use Stimulsoft\Enums\StiDataType;
use Stimulsoft\StiDataResult;
use Stimulsoft\StiHandler;
use Stimulsoft\StiPath;

class StiFileAdapter extends StiDataAdapter
{

### Properties

    /** @var string The data type loaded by the data adapter. */
    protected $dataType = StiDataType::Text;

    /** @var StiPath Link to the created database connection driver. */
    protected $connectionLink;


### Helpers

    private function getHandler(): ?StiHandler
    {
        return $this->handler instanceof StiHandler ? $this->handler : null;
    }

    private function isAllowedExtension(string $path): bool
    {
        $extensions = $this->getHandler()->fileDataExtensions;
        if ($extensions === null)
            return true;

        $path = str_replace('\\', '/', explode('?', $path)[0]);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($extension, array_map('strtolower', $extensions), true);
    }

    /** Checks the file path before accessing the file system, returns an error message if the path is not allowed. */
    private function checkConnectionString(): ?string
    {
        $connectionString = $this->connectionString ?? '';

        if (StiPath::isUrl($connectionString)) {
            if ($this->getHandler() !== null && !$this->getHandler()->allowFileDataUrls)
                return 'Loading file data by URL is disabled.';

            return null;
        }

        // Stream wrappers such as phar:// or php:// are not allowed
        if (preg_match('~^[a-z][a-z0-9+.\-]+://~i', $connectionString))
            return "Data file '$connectionString' is not allowed.";

        if ($this->getHandler() !== null && !$this->isAllowedExtension($connectionString))
            return "Data file '$connectionString' is not allowed.";

        return null;
    }

    /** Checks the real path of the found file, which may differ because of symbolic links. */
    private function checkFilePath(string $filePath): ?string
    {
        if ($this->getHandler() === null)
            return null;

        $path = realpath($filePath);
        if ($path === false || !$this->isAllowedExtension($path))
            return "Data file '$this->connectionString' is not allowed.";

        return null;
    }


### Methods

    protected function connect(): StiDataResult
    {
        $error = $this->checkConnectionString();
        if ($error !== null)
            return StiDataResult::getError($error)->getDataAdapterResult($this);

        $checkFileNames = $this->getHandler() !== null ? $this->getHandler()->checkFileNames : true;
        $path = new StiPath($this->connectionString, $checkFileNames);
        if ($path->filePath == null)
            return StiDataResult::getError("Data file '$this->connectionString' not found.")->getDataAdapterResult($this);

        if ($path->fileUrl === null) {
            $error = $this->checkFilePath($path->filePath);
            if ($error !== null)
                return StiDataResult::getError($error)->getDataAdapterResult($this);
        }

        $this->connectionLink = $path;

        return StiDataResult::getSuccess()->getDataAdapterResult($this);
    }

    public function getDataResult(?string $queryString, ?int $maxDataRows = -1): StiDataResult
    {
        $this->connectionString = $queryString;
        $this->process();

        $result = $this->connect();
        if ($result->success) {
            try {
                $result->data = file_get_contents($this->connectionLink->filePath);
                $result->dataType = $this->dataType;
            }
            catch (Exception $e) {
                $message = $e->getMessage();
                $result = StiDataResult::getError($message)->getDataAdapterResult($this);
            }
        }

        return $result;
    }
}