<?php

namespace spec\Alfabank\Client;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CardSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Alfabank\Client\Card');
    }
}
