<?php
namespace Application\Service\Nexmo;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClientFactory implements AbstractFactoryInterface
{
    protected $services = [
        'Nexmo\Sms',
        'Nexmo\Voice',
        'Nexmo\Developer',
        'Nexmo\Verify',
        'Nexmo\Insight'
    ];

    protected $config = [];

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
        if(!in_array($requestedName, $this->services)){
            return false;
        }

        if(empty($this->config) AND !$serviceLocator->has('config')){
            return false;
        }

        $this->config = $serviceLocator->get('config');

        if(!isset($this->config['nexmo'])){
            return false;
        }

        return true;
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
        return new $requestedName($this->config['nexmo']);
    }
}