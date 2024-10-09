<?php

namespace Stimulsoft;

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
        return 'application/json';
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
        $result = json_encode($this->result, JSON_UNESCAPED_SLASHES);
        return $this->handler->request->encryptData ? str_rot13(base64_encode($result)) : $result;
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