<?php
namespace Application\Service\CloudConvert;

use CloudConvert\Api;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClientFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        if(!isset($config['cloudconvert']) OR !isset($config['cloudconvert']['key'])){
            throw new \RuntimeException('missing cloudconvert config');
        }

        return new Api($config['cloudconvert']['key']);
    }

}