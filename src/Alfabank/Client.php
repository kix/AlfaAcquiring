<?php

namespace Alfabank;

use Alfabank\Client\Configuration;
use Alfabank\Client\Exception\OrderNumberNonUnique;
use Alfabank\Client\Exception\OrderNumberNotSpecified;
use Alfabank\Client\Order\AbstractStatus;

class Client
{

    private $client;

    private $configuration;

    const PREFIX = '/testpayment/rest/';

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->client = new \GuzzleHttp\Client([
            'base_url' => $this->configuration->getBaseUrl(),
            'defaults' => [
                'query' => [
                    'userName' => $this->configuration->getUsername(),
                    'password' => $this->configuration->getPassword(),
                ]
            ],
        ]);
    }

    public function registerOrder(Order $order)
    {
        $request = $this->client->createRequest('GET', self::PREFIX . 'register.do', [
            'query' => $order->toArray(),
        ]);

        $response = $this->client->send($request);
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

        $request = $this->client->createRequest('GET', self::PREFIX . 'getOrderStatusExtended.do', [
            'query' => ['orderNumber' => $number]
        ]);

        $response = $this->client->send($request);

        return AbstractStatus::fromJson($response->getBody());
    }
}
