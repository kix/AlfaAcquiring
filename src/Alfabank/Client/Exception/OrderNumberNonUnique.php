<?php
/**
 * Created by PhpStorm.
 * User: kix
 * Date: 25/11/14
 * Time: 23:58
 */

namespace Alfabank\Client\Exception;

use Alfabank\Order;

final class OrderNumberNonUnique extends \Exception
{

    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getOrder()
    {
        return $this->order;
    }

}