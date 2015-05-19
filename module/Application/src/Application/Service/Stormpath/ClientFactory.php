<?php
namespace Application\Service\Stormpath;

use Stormpath\ApiKey;
use Stormpath\Client;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClientFactory implements AbstractFactoryInterface
{
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        switch($requestedName){
            case 'Stormpath\Client':
            case 'Stormpath\Resource\Application':
                return true;
                break;
        }

        return false;
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('config');
        if(!isset($config['stormpath'])){
            throw new \RuntimeException('missing stormpath config');
        }

        $stormpath = $config['stormpath'];

        switch($requestedName){
            case 'Stormpath\Client':
                if(!isset($stormpath['key']) OR !isset($stormpath['secret'])){
                    throw new \RuntimeException('missing stormpath credentials');
                }

                //Stormpath has an odd way of wanting the credentials.
                $creds = sprintf("apiKey.id=%s\napiKey.secret=%s", $stormpath['key'], $stormpath['secret']);

                $builder = new \Stormpath\ClientBuilder();
                return $builder->setApiKeyProperties($creds)->build();
            case 'Stormpath\Resource\Application':
                if(!isset($stormpath['application'])){
                    throw new \RuntimeException('missing stormpath application');
                }

                //Application (the useful part of the client), can be gotten from the client
                $client = $serviceLocator->get('Stormpath\Client');
                $href = 'https://api.stormpath.com/v1/applications/' . $stormpath['application'];
                return $client->dataStore->getResource($href, \Stormpath\Stormpath::APPLICATION);
        }
    }
}