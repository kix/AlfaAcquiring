<?php

namespace spec\Alfabank;

use Alfabank\Adapter\AdapterInterface;
use Alfabank\Adapter\GuzzleAdapter;
use Alfabank\Client\Card;
use Alfabank\Client\Configuration;
use Alfabank\Http\Response;
use Alfabank\Order;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClientSpec extends ObjectBehavior
{

    private $adapter;

    function let(Configuration $configuration, GuzzleAdapter $adapter)
    {
        $configuration->getBaseUrl()->willReturn('https://test.paymentgate.ru');
        $configuration->getPassword()->willReturn(getenv('ALFA_PASSWORD'));
        $this->adapter = $adapter;

        $this->adapter->request(Argument::exact('GET'), Argument::exact('register.do'), Argument::type('array'))
                      ->willReturn(new Response([], '{"orderId":"94610756-9414-40cb-b73c-a810bb7191f5","formUrl":"https://test.paymentgate.ru/testpayment/merchants/merchant/payment_ru.html?mdOrder=94610756-9414-40cb-b73c-a810bb7191f5"}'));


        $this->adapter->request(Argument::exact('GET'), Argument::exact('getOrderStatusExtended.do'), Argument::type('array'))
                      ->willReturn(new Response([], '{"errorCode":"0","errorMessage":"Успешно","orderNumber":"1000","orderStatus":6,"actionCode":-2007,"actionCodeDescription":"Время сессии истекло","amount":1000,"currency":"810","date":1416937626367,"orderDescription":"null","merchantOrderParams":[],"attributes":[{"name":"mdOrder","value":"6ff7d65d-3092-411f-8b7d-e08755bda550"}],"terminalId":"12345678","paymentAmountInfo":{"paymentState":"DECLINED","approvedAmount":0,"depositedAmount":0,"refundedAmount":0},"bankInfo":{"bankCountryCode":"UNKNOWN","bankCountryName":"<Неизвестно>"}}'));

        $this->adapter->request('GET', 'reverse.do', Argument::type('array'))
                      ->willReturn(new Response([], '{"errorCode": "0"}'));

        $this->adapter->request(Argument::any(), Argument::exact("refund.do"), Argument::any())
            ->willReturn(new Response([], '{"errorCode": "0"}'));

        $this->adapter->request(Argument::any(), Argument::exact("verifyEnrollment.do"), Argument::exact(['query' => ['pan' => '4111111111111111']]))
                      ->willReturn(new Response([], '{"errorCode": "0", "errorMessage": "Успешно", "emitterName": "TEST CARD", "emitterCountryCode": "RU", "enrolled": "Y"}'));

        $this->adapter->request(Argument::any(), Argument::exact("verifyEnrollment.do"), Argument::exact(['query' => ['pan' => '639002000000000003']]))
            ->willReturn(new Response([], '{"errorCode": "0", "errorMessage": "Успешно", "emitterName": "TEST CARD", "emitterCountryCode": "RU", "enrolled": "Y"}'));

        $this->adapter->request(Argument::any(), Argument::exact("verifyEnrollment.do"), Argument::exact(['query' => ['pan' => '5555555555555599']]))
            ->willReturn(new Response([], '{"errorCode": "0", "errorMessage": "Успешно", "emitterName": "TEST CARD", "emitterCountryCode": "RU", "enrolled": "Y"}'));

        $this->beConstructedWith($configuration, $this->adapter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Alfabank\Client');
    }

    function it_registers_orders(Order $order)
    {
        $number = rand(1000, 2000);
        $order->toArray()->willReturn([
            'orderNumber' => $number,
            'amount' => 10050,
            'currency' => 810,
            'returnUrl' => 'http://return-url.com',
        ]);

        $order->setId(Argument::type('string'))->willReturn();


        $this->registerOrder($order);
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

//    function it_gets_card_data()
//    {
//
//    }

    function it_cancels_orders(Order $order)
    {
        $number = rand(1000, 2000);

        $order->getNumber()->willReturn($number);

        $order->toArray()->willReturn([
            'orderNumber' => $number,
            'amount' => 10050,
            'currency' => 810,
            'returnUrl' => 'http://return-url.com',
        ]);

        $order->setId(Argument::any())->shouldBeCalled();

        $this->registerOrder($order);

        $this->getOrderStatus($order)->shouldReturnAnInstanceOf('Alfabank\Client\Order\AbstractStatus');

        $this->cancelOrder($order);

        $this->getOrderStatus($order)->shouldReturnAnInstanceOf('Alfabank\Client\Order\Status\AuthDenied');
    }

    function it_refunds_orders(Order $order)
    {
        $number = rand(1000, 2000);

        $order->getNumber()->willReturn($number);

        $order->toArray()->willReturn([
            'orderNumber' => $number,
            'amount' => 10050,
            'currency' => 810,
            'returnUrl' => 'http://return-url.com',
        ]);

        $order->setId(Argument::any())->shouldBeCalled();

        $this->registerOrder($order);

        $this->getOrderStatus($order)->shouldReturnAnInstanceOf('Alfabank\Client\Order\AbstractStatus');

        $this->refundOrder($order)->shouldReturn(true);

        $this->getOrderStatus($order)->shouldReturnAnInstanceOf('Alfabank\Client\Order\Status\Returned');
    }

    function it_checks_card_3ds_enrollment(Card $card)
    {
        $card->getNumber()->willReturn('4111111111111111');
        $this->check3ds($card)->shouldReturn(true);

        $card->getNumber()->willReturn('639002000000000003');
        $this->check3ds($card)->shouldReturn(true);

        $card->getNumber()->willReturn('5555555555555599');
        $this->check3ds($card)->shouldReturn(false);
    }

}
