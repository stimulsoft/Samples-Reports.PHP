<?php

namespace Stimulsoft;

use Exception;
use PharData;
use Stimulsoft\Enums\StiDatabaseType;
use Stimulsoft\Enums\StiHtmlMode;
use Stimulsoft\StiFunctions;
use ZipArchive;

class StiNodeJs
{

### Options

    public $id = '';
    public $version = '22.12.0';
    public $system = '';
    public $processor = '';
    public $architecture = '';
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
            case "WIN32":
            case "WINNT":
            case "Windows":
                return "win";

            case "Darwin":
                return "darwin";

            default:
                return "linux";
        }
    }

    private function getProcessor(): string
    {
        return php_uname("m");
    }

    private function getArchitecture()
    {
        $processor = $this->getProcessor();
        $bits = PHP_INT_SIZE * 8;
        return StiFunctions::startsWith($processor, "arm") ? "arm$bits" : "x$bits";
    }

    private function getProduct(): string
    {
        return StiFunctions::isDashboardsProduct() ? "dashboards" : "reports";
    }


### Handler

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

    private static function getHandlerUrl($url): string {
        if (StiFunctions::isNullOrEmpty($url))
            $url = $_SERVER["PHP_SELF"];

        else if (StiFunctions::startsWith($url, "?"))
            $url = $_SERVER["PHP_SELF"] . $url;

        if (StiFunctions::startsWith($url, "http:") || StiFunctions::startsWith($url, "https:"))
            return $url;

        $protocol = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"];

        if (StiFunctions::startsWith($url, "/"))
            $url = mb_substr($url, 1);

        return "$protocol://$host/$url";
    }

    private function getHandlerScript(): string
    {
        $handler = $this->getHandler();
        $handler->url = self::getHandlerUrl($handler->getUrl());
        $script = $handler->getHtml(StiHtmlMode::Scripts);
        return str_replace("Stimulsoft.handler.send", "Stimulsoft.handler.https", $script);
    }


### Helpers

    private function clearError()
    {
        $this->error = null;
        $this->errorStack = null;
    }

    private function getNodeError($returnError, int $returnCode)
    {
        $lines = is_array($returnError) ? $returnError : explode("\n", $returnError ?? "");
        $npmError = false;
        $errors = ["npm ERR", "Error", "SyntaxError", "ReferenceError", "TypeError", "RequestError"];
        foreach ($lines as $line) {
            if (!StiFunctions::isNullOrEmpty($line)) {
                foreach ($errors as $error) {
                    if (mb_substr($line, 0, strlen($error)) == $error) {
                        if (mb_substr($line, 0, 3) == "npm" && !$npmError) {
                            $npmError = true;
                            continue;
                        }
                        return preg_replace("/\r/", "", $line);
                    }

                    // Handling a parser error from StiHandler
                    if (substr($line, 0, 1) == "[" && mb_strpos($line, "StiHandler") > 0 && mb_strpos($line, "StiHandler") < 10)
                        return preg_replace("/\r/", "", $line);
                }
            }
        }

        if ($returnCode !== 0)
        {
            foreach ($lines as $line)
                if (!StiFunctions::isNullOrEmpty($line))
                    return $line;

            return "ExecErrorCode: $returnCode";
        }

        return null;
    }

    private function getNodeErrorStack($returnError)
    {
        if (is_array($returnError))
            return $returnError;

        $returnError = preg_replace("/\r\n/", "\n", $returnError);
        return StiFunctions::isNullOrEmpty($returnError) ? null : explode( "\n", $returnError);
    }

    private function getSystemPath($app)
    {
        if ($this->system == "win") {
            $execResult = shell_exec("where /F $app") ?? "";
            $result = trim($execResult, "\n\r");
            $lines = explode("\n", $result);
            return trim($lines[0], "\"");
        }
        else {
            $descriptors = [
                0 => ["pipe", "r"],
                1 => ["pipe", "w"],
                2 => ["pipe", "w"],
            ];

            $pipes = [];
            $process = proc_open("which " . escapeshellarg($app), $descriptors, $pipes);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);

            return trim($output, "\n\r");
        }
    }

    private function getInstallPath()
    {
        $vendor = StiPath::getVendorPath();
        return StiPath::normalize("$vendor/nodejs-v$this->version");
    }

    private function setEnvPath($app)
    {
        $appPath = dirname(realpath($app));
        $path = getenv('PATH');
        if (strpos($path, $appPath) === false) {
            $separator = $this->system == "win" ? ";" : ":";
            $newPath = "$path$separator$appPath";
            putenv('PATH=' . $newPath);
        }
    }


### Paths

    private function getArchiveName()
    {
        $architecture = $this->processor == "armv6l" || $this->processor == "armv7l" ? $this->processor : $this->architecture;
        $extension = $this->system == "win" ? "zip" : "tar.gz";

        return "node-v$this->version-$this->system-$architecture.$extension";
    }

    private function getArchiveUrl()
    {
        $archiveName = $this->getArchiveName();
        return "https://nodejs.org/download/release/v$this->version/$archiveName";
    }

    private function getArchivePath()
    {
        $installPath = $this->getInstallPath();
        $archiveName = $this->getArchiveName();
        return StiPath::normalize("$installPath/$archiveName");
    }

    private function getApplicationPath($app)
    {
        $appPath = $this->getSystemPath($app);
        if (!StiFunctions::isNullOrEmpty($appPath))
            return $appPath;

        $path = StiFunctions::isNullOrEmpty($this->binDirectory) ? $this->getInstallPath() : $this->binDirectory;
        $path = StiPath::normalize($path);

        $appPath = StiPath::normalize("$path/$app");
        if (is_file($appPath)) {
            $this->setEnvPath($appPath);
            return $appPath;
        }

        $appPath = StiPath::normalize("$path/bin/$app");
        if (is_file($appPath)) {
            $this->setEnvPath($appPath);
            return $appPath;
        }

        $this->error = "The executable file \"$app\" was not found in the \"$path\" directory.";
        return false;
    }

    /**
     * Returns the full path to the Node executable, or false if the file was not found.
     * @return false|string
     */
    public function getNodePath()
    {
        $app = $this->system == "win" ? "node.exe" : "node";
        return $this->getApplicationPath($app);
    }

    /**
     * Returns the full path to the Npm executable, or false if the file was not found.
     * @return false|string
     */
    public function getNpmPath()
    {
        $app = $this->system == "win" ? "npm.cmd" : "npm";
        return $this->getApplicationPath($app);
    }


### Methods

    private function download()
    {
        $installPath = $this->getInstallPath();
        $archiveUrl = $this->getArchiveUrl();
        $archivePath = $this->getArchivePath();

        try {
            if (!file_exists($installPath))
                mkdir($installPath, 0775, true);

            $curl = curl_init($archiveUrl);

            $fp = fopen($archivePath, "wb");
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

        $fileSize = filesize($archivePath);
        if ($fileSize === false || $fileSize < 10000) {
            if ($fileSize !== false) {
                try {
                    unlink($archivePath);
                }
                catch (Exception $e) {
                }
            }

            $this->error = "The archive \"$archiveUrl\" was not found.";
            return false;
        }

        return true;
    }

    private function move($from, $to)
    {
        $files = scandir($from);
        foreach ($files as $name) {
            if ($name != "." && $name != "..")
                rename("$from/$name", "$to/$name");
        }

        rmdir($from);
    }

    private function extract()
    {
        $installPath = $this->getInstallPath();
        $archivePath = $this->getArchivePath();

        $output = null;
        $result = null;
        try {
            if ($this->system == "win") {
                $zip = new ZipArchive;
                $zip->open($archivePath);
                $zip->extractTo($installPath);
                $zip->close();

                $archiveBasePath = substr($archivePath, 0, -4);
                $this->move($archiveBasePath, $installPath);
                $result = 0;
            }
            else {
                exec("tar -xvf " . escapeshellarg($archivePath) . " -C " . escapeshellarg($installPath) . " --strip 1", $output, $result);
            }

            unlink($archivePath);
        }
        catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

        if ($result !== 0)
            $this->error = "An error occurred while extracting the archive \"$archivePath\" [$result].";

        return $result === 0;
    }

    private function exec(string $command, string $input, string $cwd, &$output, &$error): int
    {
        $descriptors = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];

        $pipes = [];
        $process = proc_open($command, $descriptors, $pipes, $cwd);
        if (is_resource($process)) {
            fwrite($pipes[0], $input);
            fclose($pipes[0]);

            //stream_set_blocking($pipes[1], false);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            //stream_set_blocking($pipes[2], false);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            return proc_close($process);
        }

        return -1;
    }

    /**
     * Installs the version of Node.js specified in the parameters into the vendor directory from the official website.
     * @return bool Boolean execution result.
     */
    public function installNodeJS(): bool
    {
        $this->clearError();
        $nodePath = $this->getNodePath();

        if ($nodePath === false) {
            $this->clearError();

            if ($this->download() === false)
                return false;

            if ($this->extract() === false)
                return false;
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
        if ($npmPath === false)
            return false;

        $product = $this->getProduct();
        $version = $this->getVersion();
        $command = "\"$npmPath\" install stimulsoft-$product-js@$version";

        $result = $this->exec($command, "", $this->workingDirectory, $output, $error);

        $errorText = !StiFunctions::isNullOrEmpty($error) ? $error : $output;
        $this->error = $this->getNodeError($errorText, $result);
        $this->errorStack = $this->getNodeErrorStack($errorText);

        return StiFunctions::isNullOrEmpty($this->error);
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
        if ($nodePath === false)
            return false;

        $product = $this->getProduct();
        $require = "var Stimulsoft = require('stimulsoft-$product-js');\n";
        $handler = $this->getHandlerScript();
        $command = "\"$nodePath\" 2>&1";
        $input = "$require\n$handler\n$script";

        $result = $this->exec($command, $input, $this->workingDirectory, $output, $error);

        $errorText = !StiFunctions::isNullOrEmpty($error) ? $error : $output;
        $this->error = $this->getNodeError($errorText, $result);
        $this->errorStack = $this->getNodeErrorStack($errorText);

        if (!StiFunctions::isNullOrEmpty($this->error))
            return false;

        if (!StiFunctions::isNullOrEmpty($output)) {
            try {
                $jsonStart = mb_strpos($output, $this->id) + strlen($this->id);
                $jsonLength = mb_strpos($output, $this->id, $jsonStart) - $jsonStart;
                $json = mb_substr($output, $jsonStart, $jsonLength);
                $jsonObject = json_decode($json);

                if ($jsonLength < 0 || $jsonObject === null) {
                    $this->error = "The report generator script did not return a response.";
                    return false;
                }

                switch ($jsonObject->type) {
                    case "string":
                        return $jsonObject->data;

                    case "bytes":
                        return base64_decode($jsonObject->data);
                }
            }
            catch (Exception $e) {
                $this->error = "ParseError: " . $e->getMessage();
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
        $this->processor = $this->getProcessor();
        $this->architecture = $this->getArchitecture();
        $this->workingDirectory = getcwd();
    }
}