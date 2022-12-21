<?php

namespace Stimulsoft;

class StiLicense
{
    public $isHtmlRendered = false;
    private $licenseKey;
    private $licenseFile;

    private function clearKey()
    {
        $this->licenseKey = null;
        $this->licenseFile = null;
    }

    /** Set the license key in Base64 format. */
    public function setKey($key)
    {
        $this->clearKey();
        $this->licenseKey = $key;
    }

    /** Set the path or URL to the license key file. */
    public function setFile($file)
    {
        $this->clearKey();
        $this->licenseFile = $file;
    }

    /** Get the HTML representation of the component. */
    public function getHtml()
    {
        $result = '';
        if (strlen($this->licenseKey) > 0)
            $result .= "Stimulsoft.Base.StiLicense.Key = '$this->licenseKey';\n";

        else if (strlen($this->licenseFile) > 0)
            $result .= "Stimulsoft.Base.StiLicense.loadFromFile('$this->licenseFile');\n";

        $this->isHtmlRendered = true;
        return $result;
    }

    /** Output of the HTML representation of the component. */
    public function renderHtml()
    {
        echo $this->getHtml();
    }
}