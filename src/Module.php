<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Adteam\Core\Authorization;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;
use Adteam\Core\Authorization\AuthorizationListener;
use ZF\MvcAuth\MvcAuthEvent;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // Wire in our listener at priority >1 to ensure it runs before the
        // DefaultAuthorizationListener
        $eventManager->attach(
            MvcAuthEvent::EVENT_AUTHORIZATION,
            new AuthorizationListener,
            100
        );
    }    
}
