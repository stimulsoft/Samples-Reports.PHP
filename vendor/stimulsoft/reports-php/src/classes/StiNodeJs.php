<?php

namespace Stimulsoft;

use Exception;
use PharData;
use Stimulsoft\Enums\StiDatabaseType;
use Stimulsoft\Enums\StiHtmlMode;
use ZipArchive;

class StiNodeJs
{

### Options

    public $id = '';
    public $version = '20.12.2';
    public $architecture = 'x64';
    public $system = '';
    public $binDirectory = '';
    public $workingDirectory = '';


### Properties

    /** @var StiHandler */
    private $handler;

    /** @var StiComponent */
    private $component;

    /** @var string Main text of the last error. */
    public $error;

    /** @var array Full text of the last error as an array of strings. */
    public $errorStack;


### Parameters

    private function getSystem(): string
    {
        switch (PHP_OS) {
            case 'WIN32':
            case 'WINNT':
            case 'Windows':
                return 'win';

            case 'Darwin':
                return 'darwin';

            default:
                return 'linux';
        }
    }

    private function getArchiveName(): string
    {
        $extension = $this->system == 'win' ? '.zip' : '.tar.gz';
        return $this->getDirectoryName() . $extension;
    }

    private function getDirectoryName(): string
    {
        return "node-v$this->version-$this->system-$this->architecture";
    }

    private function getDirectory(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . $this->getDirectoryName();
    }

    private function getUrl(): string
    {
        return "https://nodejs.org/dist/v$this->version/" . $this->getArchiveName();
    }

    private function getHandler(): StiHandler
    {
        if ($this->component == null && $this->handler == null)
            $this->handler = new StiHandler();

        return $this->component != null ? $this->component->handler : $this->handler;
    }

    private function getVersion(): string
    {
        return $this->getHandler()->version;
    }


### Helpers

    private function isDashboardsProduct(): bool
    {
        return class_exists('\Stimulsoft\Report\StiDashboard');
    }

    private function getHandlerUrl($url): string {
        if (StiFunctions::isNullOrEmpty($url))
            $url = $_SERVER['PHP_SELF'];

        else if (StiFunctions::startsWith($url, '?'))
            $url = $_SERVER['PHP_SELF'] . $url;

        if (StiFunctions::startsWith($url, 'http:') || StiFunctions::startsWith($url, 'https:'))
            return $url;

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];

        if (StiFunctions::startsWith($url, '/'))
            $url = mb_substr($url, 1);

        return "$protocol://$host/$url";
    }

    private function clearError()
    {
        $this->error = null;
        $this->errorStack = null;
    }

    private function getNodeError($returnError, int $returnCode)
    {
        $lines = is_array($returnError) ? $returnError : explode("\n", $returnError ?? '');
        $npmError = false;
        $errors = ['npm ERR', 'Error', 'SyntaxError', 'ReferenceError', 'TypeError', 'RequestError'];
        foreach ($lines as $line) {
            if (strlen($line ?? '') > 0) {
                foreach ($errors as $error) {
                    if (mb_substr($line, 0, strlen($error)) == $error) {
                        if (mb_substr($line, 0, 3) == 'npm' && !$npmError) {
                            $npmError = true;
                            continue;
                        }
                        return preg_replace("/\r/", '', $line);
                    }

                    // Handling a parser error from StiHandler
                    if (substr($line, 0, 1) == '[' && mb_strpos($line, 'StiHandler') > 0 && mb_strpos($line, 'StiHandler') < 10)
                        return preg_replace("/\r/", '', $line);
                }
            }
        }

        if ($returnCode !== 0)
        {
            foreach ($lines as $line)
                if (strlen($line or '') > 0)
                    return $line;

            return "ExecErrorCode: $returnCode";
        }

        return null;
    }

    private function getNodeErrorStack($returnError)
    {
        if (is_array($returnError)) return $returnError;
        $returnError = preg_replace("/\r\n/", "\n", $returnError);
        return strlen($returnError ?? '') > 0 ? explode( "\n", $returnError) : null;
    }

    private function getNodePath()
    {
        if (strlen($this->binDirectory ?? '') == 0)
            return null;

        $nodePath = $this->system == 'win'
            ? $this->binDirectory . DIRECTORY_SEPARATOR . 'node.exe'
            : $this->binDirectory . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'node';

        return is_file($nodePath) ? $nodePath : null;
    }

    private function getNpmPath()
    {
        $nodePath = $this->getNodePath();
        if (strlen($nodePath ?? '') == 0)
            return null;

        $npmPath = $this->system == 'win'
            ? mb_substr($nodePath, 0, -8) . 'npm.cmd'
            : mb_substr($nodePath, 0, -4) . 'npm';

        return is_file($npmPath) ? $npmPath : null;
    }

    private function download(): bool
    {
        $url = $this->getUrl();
        $archivePath = $this->binDirectory . DIRECTORY_SEPARATOR . $this->getArchiveName();

        try {
            if (!is_dir($this->binDirectory))
                mkdir($this->binDirectory);

            $curl = curl_init($url);

            $fp = fopen($archivePath, 'wb');
            flock($fp, LOCK_EX);

            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_FILE, $fp);
            $result = curl_exec($curl);

            curl_close($curl);
            fclose($fp);
        }
        catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

        return true;
    }

    private function unpack(): bool
    {
        $archivePath = $this->binDirectory . DIRECTORY_SEPARATOR . $this->getArchiveName();
        try {
            if ($this->system == 'win') {
                $zip = new ZipArchive;
                $zip->open($archivePath);
                $zip->extractTo($this->binDirectory);
                $zip->close();
            }
            else {
                $phar = new PharData($archivePath);
                $phar->extractTo($this->binDirectory);
            }
        }
        catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

        $sourcesPath = $this->binDirectory . DIRECTORY_SEPARATOR . $this->getDirectoryName();
        $sourceFiles = scandir($sourcesPath);
        foreach ($sourceFiles as $fileName) {
            if ($fileName != '.' && $fileName != '..')
                rename($sourcesPath . DIRECTORY_SEPARATOR . $fileName, $this->binDirectory . DIRECTORY_SEPARATOR . $fileName);
        }

        rmdir($sourcesPath);
        unlink($archivePath);

        return true;
    }

    private function getHandlerScript(): string
    {
        $handler = $this->getHandler();
        $handler->url = $this->getHandlerUrl($handler->getUrl());
        $script = $handler->getHtml(StiHtmlMode::Scripts);
        return str_replace('Stimulsoft.handler.send', 'Stimulsoft.handler.https', $script);
    }

    function exec(string $command, string $input, string $cwd, &$output, &$error): int
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];

        $process = proc_open($command, $descriptors, $pipes, $cwd);
        if (is_resource($process)) {
            fwrite($pipes[0], $input);
            fclose($pipes[0]);

            stream_set_blocking($pipes[1], false);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            stream_set_blocking($pipes[2], false);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            return proc_close($process);
        }

        return -1;
    }


### Methods

    /**
     * Installs the version of Node.js specified in the parameters into the working directory from the official website.
     * @return bool Boolean execution result.
     */
    public function installNodeJS(): bool
    {
        $this->clearError();
        if ($this->getNodePath() == null) {
            if (!$this->download()) return false;
            if (!$this->unpack()) return false;
        }

        return true;
    }

    /**
     * Updates product packages to the current version.
     * @return bool Boolean execution result.
     */
    public function updatePackages(): bool
    {
        $this->clearError();
        $npmPath = $this->getNpmPath();
        $product = $this->isDashboardsProduct() ? 'dashboards' : 'reports';
        $version = $this->getVersion();
        $command = "\"$npmPath\" install stimulsoft-$product-js@$version";
        $result = $this->exec($command, '', $this->workingDirectory, $output, $error);
        $this->error = strlen($error || '') > 0 ? $this->getNodeError($error, $result) : $this->getNodeError($output, $result);
        $this->errorStack = strlen($error || '') > 0 ? $this->getNodeErrorStack($error) : $this->getNodeErrorStack($output);
        $this->errorStack = $this->getNodeErrorStack($error);
        return strlen($this->error ?? '') == 0;
    }

    /**
     * Executes server-side script using Node.js
     * @param string $script JavaScript prepared for execution in Node.js
     * @return string|bool Depending on the script, it returns a string data or a bool result.
     */
    public function run(string $script)
    {
        $this->clearError();
        $nodePath = $this->getNodePath();
        if ($nodePath == null) {
            $this->error = 'The path to the Node.js not found.';
            return false;
        }

        $product = $this->isDashboardsProduct() ? 'dashboards' : 'reports';
        $require = "var Stimulsoft = require('stimulsoft-$product-js');\n";
        $handler = $this->getHandlerScript();
        $command = "\"$nodePath\" 2>&1";
        $input = "$require\n$handler\n$script";
        $result = $this->exec($command, $input, $this->workingDirectory, $output, $error);
        $this->error = strlen($error || '') > 0 ? $this->getNodeError($error, $result) : $this->getNodeError($output, $result);
        $this->errorStack = strlen($error || '') > 0 ? $this->getNodeErrorStack($error) : $this->getNodeErrorStack($output);
        if (strlen($this->error ?? '') > 0)
            return false;

        if (strlen($output or '') > 0) {
            try {
                $jsonStart = mb_strpos($output, $this->id) + strlen($this->id);
                $jsonLength = mb_strpos($output, $this->id, $jsonStart) - $jsonStart;
                $json = mb_substr($output, $jsonStart, $jsonLength);
                $jsonObject = json_decode($json);
                if ($jsonObject->type == 'string') return $jsonObject->data;
                if ($jsonObject->type == 'bytes') return base64_decode($jsonObject->data);
            }
            catch (Exception $e) {
                $this->error = 'ParseError: ' . $e->getMessage();
                return false;
            }
        }

        return true;
    }


### Constructor

    public function __construct(StiComponent $component = null)
    {
        $this->id = StiFunctions::newGuid();
        $this->component = $component;
        $this->system = $this->getSystem();
        $this->binDirectory = $this->getDirectory();
        $this->workingDirectory = getcwd();
    }
}