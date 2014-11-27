<?php
namespace Alfabank\Adapter;

use Alfabank\Client\Configuration;
use Alfabank\Http\Response;

class GuzzleAdapter implements AdapterInterface
{

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->baseUrl = $this->configuration->getBaseUrl();

        $this->guzzle = new \GuzzleHttp\Client([
            'defaults' => [
                'query' => [
                    'userName' => $this->configuration->getUsername(),
                    'password' => $this->configuration->getPassword(),
                ]
            ],
        ]);
    }

    public function request($method, $url, $options)
    {
        $request = $this->guzzle->createRequest($method, $this->baseUrl . 'register.do', [
            'query' => $options['query'],
        ]);

        $response = $this->guzzle->send($request);

        $body = $response->getBody();
        $bodyContent = stream_get_contents($body);

        return new Response(
            $response->getHeaders(),
            $bodyContent
        );
    }


}