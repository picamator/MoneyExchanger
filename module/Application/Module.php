<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Model\Converter;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Log\Logger;

use Application\Service\ErrorHandling as ErrorHandlingService;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
           
        $application    = $e->getTarget();
        $services       = $application->getServiceManager();
        
        // set phpSettings
        $config = $services->get('Config');
        if (isset($config['phpSettings'])) {
            foreach($config['phpSettings'] as $key => $value) {
                ini_set($key, $value);
            }
        }
        
        // add event listeners for handling Exceptions
        // @link Rob Allen: http://akrabat.com/zend-framework-2/simple-logging-of-zf2-exceptions/
        $eventManager->attach('dispatch.error', function ($event) use ($services) {
            $exception = $event->getResult()->exception;            
            if (!$exception) {
                return;
            }
            $service = $services->get('ApplicationServiceErrorHandling');
            $service->logException($exception);
        });
        
        // add error handler
        $logger = $services->get('ZendLog');
        Logger::registerErrorHandler($logger);                        
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
    
    public function getViewHelperConfig()   
    {
        return array(
            'invokables' => array(
                'FormCollection'    => 'Application\View\Helper\FormCollection',
                'FormElement'       => 'Application\View\Helper\FormElement',
            )
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'ApplicationServiceErrorHandling' =>  function($sm) {
                    $logger = $sm->get('ZendLog');
                    
                    // exception handler
                    $service = new ErrorHandlingService($logger);
                    
                    return $service;
                },
                'ZendLog' => function ($sm) {   
                    $config = $sm->get('Config');
                    if (! isset($config['logger']) ||
                        ! is_array($config['logger'])) {
                         throw new \RuntimeException();
                    }
                                       
                    return new Logger($config['logger']);
                },
                'Converter' => function($sm) {
                    $translator = $sm->get('translator');
                    return new Converter($translator);
                }        
            ),
        );
    }
}
