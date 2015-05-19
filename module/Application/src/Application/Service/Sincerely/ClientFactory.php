<?php
namespace Application\Service\Sincerely;

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
        if(!isset($config['sincerely']) OR !isset($config['sincerely']['key'])){
            throw new \RuntimeException('missing sincerely config');
        }

        return new Client($config['sincerely']['key'], 'https://snapi-sincerely-com-4qhc22q3lku6.runscope.net/shiplib');
    }

}