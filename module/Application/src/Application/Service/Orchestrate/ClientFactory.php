<?php
namespace Application\Service\Orchestrate;

use andrefelipe\Orchestrate\Application;
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
        if(!isset($config['orchestrate']) OR !isset($config['orchestrate']['key'])){
            throw new \RuntimeException('missing orchestrate config');
        }

        //Application (not client) lets us use bit more less verbose methods
        return new Application($config['orchestrate']['key']);
    }

}