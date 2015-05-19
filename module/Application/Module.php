<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], -1000);
    }

    public function onRoute(MvcEvent $e)
    {
        /* @var $auth \Zend\Authentication\AuthenticationService */
        $auth = $e->getApplication()->getServiceManager()->get('Zend\Authentication\AuthenticationService');

        $routeMatch = $e->getRouteMatch();

        if(!$auth->hasIdentity() AND $routeMatch->getParam('controller') == 'Application\Controller\Index'){
            $e->getRouteMatch()->setMatchedRouteName('signup');
            $e->getRouteMatch()->setParam('action', 'signup')
                               ->setParam('controller', 'Application\Controller\Signin');
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
