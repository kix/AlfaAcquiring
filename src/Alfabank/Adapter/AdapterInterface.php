<?php
/**
 * Created by PhpStorm.
 * User: kix
 * Date: 27/11/14
 * Time: 19:38
 */

namespace Alfabank\Adapter;

use Alfabank\Client\Configuration;

interface AdapterInterface
{

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration);

    /**
     * @param $method
     * @param $url
     * @param $options
     *
     * @return \Alfabank\Http\Response
     */
    public function request($method, $url, $options);

}