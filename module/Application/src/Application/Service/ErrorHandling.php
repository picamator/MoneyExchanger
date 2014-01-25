<?php
/**
 * Exception Handler
 */
namespace Application\Service;
use Zend\Log\Logger;

class ErrorHandling
{
    protected $logger;
    
    /**
     * @param \Zend\Log\Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * Exception handler
     * 
     * @param \Exception $e
     */
    public function logException(\Exception $e)
    {
        $trace = $e->getTraceAsString();
        $i = 1;
        do {
            $messages[] = $i++ . ': ' . $e->getMessage();
        } while ($e = $e->getPrevious());

        $log = "Exception:n" . implode("n", $messages);
        $log .= "nTrace:n" . $trace;

        $this->logger->err($log);
    }
}