<?php

namespace Stimulsoft;

use Stimulsoft\Enums\StiDataType;

/**
 * The result of executing an event handler request. You can get the data, its type
 * and other parameters necessary to create a web server response.
 */
class StiBaseResponse
{

### Properties

    /** @var StiBaseHandler */
    public $handler = null;

    /** @var StiBaseResult */
    public $result = null;


### Helpers

    /**
     * Returns the detected origin url for the handler response. Can be used for the 'Access-Control-Allow-Origin' header of the response.
     */
    public function getOrigin(): string
    {
        return $this->handler->origin;
    }

    /**
     * Returns the mime-type for the handler response.
     */
    public function getMimeType(): string
    {
        if ($this->result instanceof StiDataResult && $this->result->dataType !== null)
            return $this->result->dataType;

        return StiDataType::JSON;
    }

    /**
     * Returns the content type for the handler response. Can be used for the 'Content-Type' header of the response.
     */
    public function getContentType(): string
    {
        return $this->getMimeType() . '; charset=utf-8';
    }

    /**
     * Returns the handler response as a byte string. When using encryption, the response will be encrypted and encoded into a Base64 string.
     */
    public function getData(): string
    {
        if ($this->result instanceof StiDataResult && $this->result->getType() == "File")
            return $this->result->data !== null ? $this->result->data : "";

        $result = json_encode($this->result, JSON_UNESCAPED_SLASHES);
        $encryptSqlData = $this->handler->encryptSqlData || $this->result->getType() != "SQL";
        return $this->handler->request->encryptData && $encryptSqlData ? str_rot13(base64_encode($result)) : $result;
    }


### Response

    /**
     * Outputs the result along with all necessary headers, and exits.
     */
    public function printData()
    {
        $data = $this->getData();
        if (!headers_sent()) {
            header('Content-Type: ' . $this->getContentType());
            header('Content-Length: ' . strlen($data));
            header('Cache-Control: no-cache');
            header("X-Stimulsoft-Result: " . $this->result->getType());
        }
        echo $data;
        exit();
    }


### Constructor

    public function __construct($handler, $result = null)
    {
        $this->handler = $handler;
        $this->result = $result !== null ? $result : $this->handler->getResult();
    }
}