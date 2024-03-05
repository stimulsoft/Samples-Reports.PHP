<?php

namespace Stimulsoft;

class StiDataRequest
{
    public $encode = false;
    public $command;
    public $connectionString;
    public $queryString;
    public $parameters;
    public $database;
    public $dataSource;
    public $connection;
    public $timeout;

    private function populateVars($obj)
    {
        $className = get_class($this);
        $vars = get_class_vars($className);
        foreach ($vars as $name => $value) {
            if (isset($obj->{$name}))
                $this->{$name} = $obj->{$name};
        }
    }

    public function parse()
    {
        $input = file_get_contents('php://input');

        if (!is_null($input) && strlen($input) > 0 && mb_substr($input, 0, 1) != '{') {
            $input = str_rot13($input);
            $this->encode = true;
        }
        
        if ($this->encode)
            $input = base64_decode($input);

        $obj = json_decode($input);
        if ($obj == null) {
            $message = 'JSON parser error #' . json_last_error();
            if (function_exists('json_last_error_msg'))
                $message .= ' (' . json_last_error_msg() . ')';

            return StiResult::error($message);
        }

        $this->populateVars($obj);

        return $this->checkRequestParams($obj);
    }

    protected function checkRequestParams($obj)
    {
        if (isset($obj->command))
            $this->command = $obj->command;

        $reflectionClass = new \ReflectionClass('\Stimulsoft\StiDataCommand');
        $commands = $reflectionClass->getConstants();
        $values = array_values($commands);

        if (in_array($this->command, $values))
            return StiResult::success(null, $this);

        return StiResult::error('Unknown command [' . $this->command . ']');
    }
}