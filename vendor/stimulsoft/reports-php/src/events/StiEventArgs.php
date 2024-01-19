<?php

namespace Stimulsoft;

class StiEventArgs
{
    /** @var StiComponentType The type of the client side component that sent the request. */
    public $sender;

    /** @var StiResult The result of the event execution, which will be passed to the client side. */
    public $result;

    /** For internal use only. */
    public function populateVars($obj, $checkFileNames = false)
    {
        $className = get_class($this);
        $vars = get_class_vars($className);
        foreach ($vars as $name => $value) {
            if (isset($obj->{$name}))
                $this->{$name} = $obj->{$name};
        }

        if ($checkFileNames)
            $this->checkFileName();
    }

    public function checkFileName()
    {
        if (isset($this->fileName)) {
            $this->fileName = ltrim($this->fileName, " \n\r\t\v\x00\\\/.");
            while (preg_match('/\.\.\/\.\.|\.\.\\\.\./', $this->fileName))
                $this->fileName = preg_replace('/\.\.\/\.\.|\.\.\\\.\./', '..', $this->fileName);
        }
    }
}