<?php
namespace Application\Factory\Controller;

use Application\Controller\ClaimController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClaimFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();
        return new ClaimController(
            $serviceLocator->get('Application\Service\Orchestrate\StorageService'),
            $serviceLocator->get('Nexmo\Verify')
        );
    }
}