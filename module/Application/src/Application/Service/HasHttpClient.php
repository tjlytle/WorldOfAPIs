<?php
namespace Application\Service;

use Zend\Http\Client;

/**
 * Simple Trait to add a HttpClient to API Wrappers
 * @package Application\Service
 */
trait HasHttpClient
{
    protected $client;

    /**
     * @param Client $client
     */
    public function setHttpClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        if(empty($this->client)){
            //By default use curl (avoid SSL issues)
            $client = new Client(null, [
                'adapter' => 'Zend\Http\Client\Adapter\Curl',
            ]);

            $this->setHttpClient($client);
        }

        return $this->client;
    }
}