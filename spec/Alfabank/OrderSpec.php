<?php

namespace spec\Alfabank;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OrderSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedWith(100000, new \Money(100.60, 'RUR'), 'http://return-url.com');
    }

    function it_accepts_order_ids()
    {
        $this->setId('c0fb4226-4f91-4297-99ff-dca0193f4b9e');

        $this->getId()->shouldReturn('c0fb4226-4f91-4297-99ff-dca0193f4b9e');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Alfabank\Order');
    }

    function it_converts_to_array()
    {
        $this->toArray()->shouldBeLike([
            'orderNumber' => 100000,
            'amount' => 10060,
            'currency' => 810,
            'returnUrl' => 'http://return-url.com',
        ]);
    }

    function it_returns_number()
    {
        $this->getNumber()->shouldEqual(100000);
    }

    function it_outputs_id_when_available()
    {
        $this->setId('c0fb4226-4f91-4297-99ff-dca0193f4b9e');

        $this->toArray()->shouldBeLike([
            'orderNumber' => 100000,
            'amount' => 10060,
            'currency' => 810,
            'orderId' => 'c0fb4226-4f91-4297-99ff-dca0193f4b9e',
            'returnUrl' => 'http://return-url.com',
        ]);
    }

}
