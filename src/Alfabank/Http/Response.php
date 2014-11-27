<?php
namespace Alfabank\Http;

class Response
{

    private $headers;

    private $body;

    public function __construct($headers, $body)
    {
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

}