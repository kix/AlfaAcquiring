<?php

namespace Alfabank;

use Alfabank\Adapter\AdapterInterface;
use Alfabank\Client\Card;
use Alfabank\Client\Configuration;
use Alfabank\Client\Exception\OrderNumberNonUnique;
use Alfabank\Client\Exception\OrderNumberNotSpecified;
use Alfabank\Client\Order\AbstractStatus;

class Client
{

    private $client;

    private $configuration;

    const PREFIX = '/testpayment/rest/';

    public function __construct(Configuration $configuration, AdapterInterface $adapter)
    {
        $this->configuration = $configuration;
        $this->adapter = $adapter;
    }

    public function registerOrder(Order $order)
    {
        $response = $this->adapter->request('GET', 'register.do', [
            'query' => $order->toArray(),
        ]);

        $result = (array) json_decode($response->getBody());

        if (array_key_exists('errorCode', $result)) {
            if ($result['errorCode'] == 1) {
                throw new OrderNumberNonUnique($order);
            }
        }

        $order->setId($result['orderId']);

        return $order;
    }


    public function getOrderStatus(Order $order)
    {
        if (!$number = $order->getNumber()) {
            throw new OrderNumberNotSpecified();
        }

        $response = $this->adapter->request('GET', 'getOrderStatusExtended.do', [
            'query' => ['orderNumber' => $number]
        ]);

        return AbstractStatus::fromJson($response->getBody());
    }

    public function cancelOrder(Order $order)
    {
        if (!$number = $order->getNumber()) {
            throw new OrderNumberNotSpecified();
        }

        $response = $this->adapter->request('GET', 'reverse.do', [
            'query' => ['orderNumber' => $number],
        ]);
    }

    public function refundOrder(Order $order)
    {
        if (!$number = $order->getNumber()) {
            throw new OrderNumberNotSpecified();
        }

        $response = $this->adapter->request('GET', 'refund.do', [
            'query' => ['orderNumber' => $number],
        ]);

        $result = (array) json_decode($response->getBody());

        return $result['errorCode'] == false;
    }

    public function check3ds(Card $card)
    {
        $response = $this->adapter->request('GET', 'verifyEnrollment.do', [
            'query' => ['pan' => $card->getNumber()],
        ]);

        $result = (array) json_decode($response->getBody());

        if ($result['enrolled'] == 'Y') {
            return true;
        }

        return false;
    }
}
