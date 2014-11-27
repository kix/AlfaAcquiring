<?php

namespace Alfabank\Client;

class Configuration
{

    private $username;

    private $password;

    private $baseUrl;

    const ENV_TEST = 'test';

    const ENV_PROD = 'prod';

    private static $environments = array(
        self::ENV_PROD,
        self::ENV_TEST
    );

    public function __construct($username, $password, $environment)
    {
        $this->username = $username;
        $this->password = $password;

        if (!in_array($environment, self::$environments)) {
            throw new \InvalidArgumentException(sprintf(
                'Environment should be one of: %s; %s given',
                implode(',', self::$environments),
                $environment
            ));
        }

        if ($environment == self::ENV_TEST) {
            $this->baseUrl = 'https://test.paymentgate.ru/testpayment/rest/';
        } else {
            throw new \Exception('Prod environment URL unknown');
        }
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

}
