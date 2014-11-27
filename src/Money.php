<?php

final class Money
{

    const RUR = 810;

    const EUR = 978;

    const USD = 840;

    private static $supportedCurrencies = array(
        'RUR' => self::RUR,
        'EUR' => self::EUR,
        'USD' => self::USD
    );

    /**
     * @var string
     */
    private $currency;

    /**
     * @var integer
     */
    private $amount;

    /**
     * @param $amount
     * @param $currency
     */
    public function __construct($amount, $currency)
    {
        if (!array_key_exists($currency, self::$supportedCurrencies)) {
            $flipped = array_flip(self::$supportedCurrencies);

            if (array_key_exists($currency, $flipped)) {
                $this->currency = $currency;
            } else {
                throw new \InvalidArgumentException(sprintf('Currency %s is not supported', $currency));
            }
        }

        $this->amount = $amount;
        if (!$this->currency) {
            $this->currency = self::$supportedCurrencies[$currency];
        }
    }

    public function toArray()
    {
        return [
            'amount'   => (int) ($this->amount * 100),
            'currency' => $this->currency,
        ];
    }
}
