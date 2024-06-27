<?php

namespace Stimulsoft;

class StiLicense extends StiElement
{

### Fields

    private static $globalKey;
    private static $globalFile;
    private $licenseKey;
    private $licenseFile;


### Helpers

    private function clearKey()
    {
        $this->licenseKey = null;
        $this->licenseFile = null;
    }


### License

    /**
     * Set the license key in Base64 format for all components.
     */
    public static function setPrimaryKey(string $key)
    {
        self::$globalKey = $key;
    }

    /**
     * Set the path or URL to the license key file for all components.
     */
    public static function setPrimaryFile(string $file)
    {
        self::$globalFile = $file;
    }

    /**
     * Set the license key in Base64 format.
     */
    public function setKey(string $key)
    {
        $this->clearKey();
        $this->licenseKey = $key;
    }

    /**
     * Set the path or URL to the license key file.
     */
    public function setFile(string $file)
    {
        $this->clearKey();
        $this->licenseFile = $file;
    }


### HTML

    public function getHtml(): string
    {
        $result = '';

        if (self::$globalKey !== null && strlen(self::$globalKey) > 0)
            $this->licenseKey = self::$globalKey;

        if (self::$globalFile !== null && strlen(self::$globalFile) > 0)
            $this->licenseFile = self::$globalFile;

        if ($this->licenseKey !== null && strlen($this->licenseKey) > 0)
            $result = "Stimulsoft.Base.StiLicense.loadFromString('$this->licenseKey');\n";

        else if ($this->licenseFile !== null && strlen($this->licenseFile) > 0)
            $result = "Stimulsoft.Base.StiLicense.loadFromFile('$this->licenseFile');\n";

        return $result . parent::getHtml();
    }
}