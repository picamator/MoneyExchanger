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

use Application\ConverterAwareInterface; 
use Application\TranslatorAwareInterface;

use Application\YahooClientAwareInterface;
use Application\Model\Yahoo\FinaceApiClient;

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
                         throw new \RuntimeException('Error: Logger configuration was not found.');
                    }
                                       
                    return new Logger($config['logger']);
                }      
            ),
        );
    }
    
    public function getControllerConfig()
    {
        return array(
            'initializers' => array(
                function ($controllers, $sm) {
                    $services   = $sm->getServiceLocator();
                    $translator = $services->get('translator');
                    // inject converter model
                    if ($controllers instanceof ConverterAwareInterface) {
                        $controllers->setConverter(new Converter($translator));
                    }
                    
                    // inject translator
                    if ($controllers instanceof TranslatorAwareInterface) {
                        $controllers->setTranslator($translator);
                    } 
                    
                    // inject Yahoo! Finance Client
                    if ($controllers instanceof YahooClientAwareInterface) {
                        $config = $services->get('Config');
                        if (! isset($config['yahooClient']) ||
                            ! is_array($config['yahooClient'])) {
                            throw new \RuntimeException('Error: Yahoo! Finance API configuration was not found.');
                        }
                        
                        $controllers->setYahooClient(new FinaceApiClient($config['yahooClient']));
                    } 
                }
            )
        );
    }
}
