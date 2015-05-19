<?php
namespace Application\Service\Lob;
use Lob\Lob;
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
        if(!isset($config['lob']) OR !isset($config['lob']['key'])){
            throw new \RuntimeException('missing lob config');
        }

        return new Lob($config['lob']['key']);
    }

}