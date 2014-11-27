<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MoneySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(100.50, 'RUR');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Money');
    }

    function it_converts_to_array()
    {
        $this->toArray()->shouldReturn([
            'amount' => 10050,
            'currency' => 810,
        ]);
    }
}
