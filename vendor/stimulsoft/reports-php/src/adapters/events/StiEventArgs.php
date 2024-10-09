<?php

namespace Stimulsoft\Events;

use ReflectionClass;
use ReflectionProperty;
use Stimulsoft\StiBaseHandler;
use Stimulsoft\StiFunctions;
use Stimulsoft\Enums\StiEventType;

class StiEventArgs
{

### Properties

    /** @var StiEventType|string [enum] Name of the current event. */
    public $event = null;

    /** @var object The component that sent the request. */
    public $sender = null;


### Helpers

    protected function getHandler()
    {
        if ($this->sender instanceof StiBaseHandler)
            return $this->sender;

        return null;
    }

    /** Returns the safe file name. */
    private function getFileName(string $fileName): string
    {
        $handler = $this->getHandler();
        if ($handler !== null && property_exists($handler, 'checkFileNames') && $handler->checkFileNames) {
            $fileName = ltrim($fileName, " \n\r\t\v\x00\\\/.");
            while (preg_match('/\.\.\/\.\.|\.\.\\\.\./', $fileName))
                $fileName = preg_replace('/\.\.\/\.\.|\.\.\\\.\./', '..', $fileName);
        }

        return $fileName;
    }

    private function setObject($object)
    {
        $properties = StiFunctions::getProperties($this, ['sender']);
        foreach ($properties as $name)
            if (property_exists($object, $name))
                $this->setProperty($name, $object->$name);
    }

    protected function setProperty($name, $value)
    {
        if ($name == 'fileName' && $value !== null)
            $value = $this->getFileName($value);

        $this->$name = $value;
    }


### Constructor

    public function __construct($obj = null)
    {
        if ($obj !== null)
            $this->setObject($obj);
    }
}