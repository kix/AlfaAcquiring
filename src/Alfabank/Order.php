<?php

namespace Alfabank;

class Order
{

    protected $number;

    protected $amount;

    protected $returnUrl;

    protected $orderId;

    public function __construct($number, \Money $amount, $returnUrl)
    {
        $this->amount = $amount;
        $this->number = $number;
        $this->returnUrl = $returnUrl;
    }

    /**
     * @param string $orderId
     */
    public function setId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->orderId;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    public function toArray()
    {
        $defaults = [
            'orderNumber' => $this->number,
            'returnUrl' => $this->returnUrl,
        ];

        if ($this->orderId) {
            $defaults = array_merge($defaults, [
                'orderId' => $this->orderId
            ]);
        }

        return array_merge($defaults, $this->amount->toArray());
    }
}
