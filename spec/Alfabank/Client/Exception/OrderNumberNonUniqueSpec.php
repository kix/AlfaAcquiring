<?php

namespace spec\Alfabank\Client\Exception;

use Alfabank\Order;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OrderNumberNonUniqueSpec extends ObjectBehavior
{
    function let(Order $order)
    {
        $this->beConstructedWith($order);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Alfabank\Client\Exception\OrderNumberNonUnique');
    }

    function it_returns_order()
    {
        $this->getOrder()->shouldReturnAnInstanceOf('Alfabank\Order');
    }
}
