<?php

namespace spec\Alfabank;

use Alfabank\Client\Configuration;
use Alfabank\Order;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClientSpec extends ObjectBehavior
{
    function let(Configuration $configuration)
    {
        $configuration->getBaseUrl()->willReturn('https://test.paymentgate.ru');
        $configuration->getPassword()->willReturn(getenv('ALFA_PASSWORD'));
        $configuration->getUsername()->willReturn(getenv('ALFA_USERNAME'));

        $this->beConstructedWith($configuration);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Alfabank\Client');
    }

    function it_registers_orders(Order $order)
    {
        $number = rand(2000, 3000);

        $order->toArray()->willReturn([
            'orderNumber' => $number,
            'amount' => 10050,
            'currency' => 810,
            'returnUrl' => 'http://return-url.com',
        ]);

        $order->setId(Argument::type('string'))->shouldBeCalled();

        $this->registerOrder($order);

        return $number;
    }

    function it_throws_an_exception_when_order_has_no_number(Order $order)
    {
        $this->shouldThrow('\Alfabank\Client\Exception\OrderNumberNotSpecified')->duringGetOrderStatus($order);
    }

    function it_throws_exception_when_an_order_number_is_non_unique(Order $order)
    {
        $order->toArray()->willReturn([
            'orderNumber' => rand(1000, 2000),
            'amount' => 10050,
            'currency' => 810,
            'returnUrl' => 'http://return-url.com',
        ]);

        $order->setId(Argument::any())->shouldBeCalled();

        $this->registerOrder($order);

        $this->shouldThrow('\Alfabank\Client\Exception\OrderNumberNonUnique')->duringRegisterOrder($order);
    }

    function it_gets_order_status(Order $order)
    {
        $order->getNumber()->willReturn(1000);

        $this->getOrderStatus($order)->shouldReturnAnInstanceOf('Alfabank\Client\Order\AbstractStatus');
    }

    function it_gets_card_data()
    {

    }

}
