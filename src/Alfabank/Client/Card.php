<?php

namespace Alfabank\Client;

class Card
{

    /**
     * @var string
     */
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

}
