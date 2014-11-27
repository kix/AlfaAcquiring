<?php

namespace spec\Alfabank\Client;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigurationSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedWith('login', 'password', 'test');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Alfabank\Client\Configuration');
    }

}
