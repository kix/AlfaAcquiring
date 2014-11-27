<?php

namespace Alfabank\Client\Order;

use Alfabank\Client\Order\Status\Registered;

abstract class AbstractStatus
{

    /**
     * Заказ зарегистрирован, но не оплачен
     */
    const STATUS_REGISTERED = 0;

    /**
     * Предавторизованная сумма захолдирована
     */
    const STATUS_HOLD = 10;

    /**
     * Проведена полная авторизация суммы заказа
     */
    const STATUS_FULLY_AUTHORIZED = 20;

    /**
     * Авторизация отменена
     */
    const STATUS_AUTH_CANCELED = 30;

    /**
     * По транзакции проведена операция возврата
     */
    const STATUS_RETURNED = 40;

    /**
     * Инициирована авторизация через ACS банка
     */
    const STATUS_AUTH_INITIATED = 50;

    /**
     * Авторизация отклонена
     */
    const STATUS_AUTH_DENIED = 60;

    private static $statuses = [
        0 => self::STATUS_REGISTERED,
        1 => self::STATUS_HOLD,
        2 => self::STATUS_FULLY_AUTHORIZED,
        3 => self::STATUS_AUTH_CANCELED,
        4 => self::STATUS_RETURNED,
        5 => self::STATUS_AUTH_INITIATED,
        6 => self::STATUS_AUTH_DENIED,
    ];

    private static $classmap = [
        self::STATUS_REGISTERED => '\Alfabank\Client\Order\Status\Registered',
        self::STATUS_HOLD => '\Alfabank\Client\Order\Status\Hold',
        self::STATUS_FULLY_AUTHORIZED => '\Alfabank\Client\Order\Status\FullyAuthorized',
        self::STATUS_AUTH_CANCELED => '\Alfabank\Client\Order\Status\AuthCanceled',
        self::STATUS_RETURNED => '\Alfabank\Client\Order\Status\Returned',
        self::STATUS_AUTH_INITIATED => '\Alfabank\Client\Order\Status\AuthInitiated',
        self::STATUS_AUTH_DENIED => '\Alfabank\Client\Order\Status\AuthDenied',
    ];

    protected $orderNumber;

    protected $actionCode;

    protected $actionCodeDesciption;

    protected $amount;

    protected $datetime;

    protected $description;

    protected $attributes;

    protected $terminalId;

    protected $amountInfo;

    public function __construct(
        $orderNumber,
        $actionCode,
        $actionCodeDesciption,
        \Money $amount,
        \DateTime $datetime,
        $description,
        $attributes,
        $terminalId,
        $amountInfo
    ) {
        $this->orderNumber = $orderNumber;
        $this->actionCode = $actionCode;
        $this->actionCodeDesciption = $actionCodeDesciption;
        $this->amount = $amount;
        $this->datetime = $datetime;
        $this->description = $description;
        $this->attributes = $attributes;
        $this->terminalId = $terminalId;
        $this->amountInfo = $amountInfo;
    }

    /**
     * Named constructor
     *
     * @param $json
     * @return AbstractStatus
     */
    public static function fromJson($json)
    {
        $data = (array) json_decode($json);

        $status = self::$statuses[$data['orderStatus']];

        $statusClassname = self::$classmap[$status];

        $dt = new \DateTime();
        $dt->setTimestamp($data['date']);

        return new $statusClassname(
            $data['orderNumber'],
            $data['actionCode'],
            $data['actionCodeDescription'],
            new \Money($data['amount'], $data['currency']),
            $dt,
            $data['orderDescription'],
            $data['attributes'],
            $data['terminalId'],
            $data['paymentAmountInfo']
        );
    }

}