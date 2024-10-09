<?php

namespace Stimulsoft;

class StiJsElement extends StiElement
{

### Helpers

    protected function getIgnoredProperties(): array
    {
        return ['id', 'htmlRendered'];
    }

    protected function getProperties(): array
    {
        $ignored = $this->getIgnoredProperties();
        return StiFunctions::getProperties($this, $ignored);
    }

    protected function getStringValue(string $name, $value)
    {
        if (is_string($value))
            return StiFunctions::isEnumeration($this, $name) ? $value : "\"$value\"";

        if ($value === 'NULL')
            return 'null';

        return json_encode($value);
    }

    protected function setProperty(string $name, $value)
    {
        if ($this->$name instanceof StiJsElement) $this->$name->setObject($value);
        else $this->$name = $value;
    }

    public function getObject(): array
    {
        $properties = $this->getProperties();
        $result = [];
        foreach ($properties as $name) {
            $value = $this->$name;
            $result[$name] = $value instanceof StiJsElement ? $value->getObject() : $value;
        }

        return $result;
    }

    public function setObject($object)
    {
        if (is_object($object))
            $object = (array)$object;

        $properties = $this->getProperties();
        foreach ($properties as $name) {
            if (array_key_exists($name, $object))
                $this->setProperty($name, $object[$name]);
        }
    }

    public function compareObject($object): bool
    {
        if ($object === null)
            return false;

        $properties = $this->getProperties();
        foreach ($properties as $name) {
            if (property_exists($object, $name)) {
                $result = $this->$name instanceof StiJsElement
                    ? $this->$name->compareObject($object->$name)
                    : $this->$name === $object->$name;

                if ($result === false)
                    return false;
            }
        }

        return true;
    }
}