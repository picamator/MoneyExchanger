<?php
namespace Application\Model\Yahoo;
use Zend\Log\Logger;

/**
 * Interface for Finance API Client
 */
interface FinaceApiClientInterface
{
    /**
     * Sets From
     *   
     * @param string $from
     * @return self
     */
    public function setFrom($from);
    
    /**
     * Gets From
     *   
     * @return string
     */
    public function getFrom();
    
    /**
     * Sets to
     * 
     * @param string $to
     * @return self
     */
    public function setTo($to);
     
    /**
     * Gets To
     *   
     * @return string
     */
    public function getTo();
    
    /**
     * Gets currency course
     * 
     * @return float
     */
    public function getCourse();
    
    /**
     * Sets Curl Options
     * 
     * @param array $curlOptions
     * @return self
     */
    public function setCurlOptions(array $curlOptions);
    
    /**
     * Sets Logger
     * 
     * @param \Zend\Log\Logger $logger
     * @return self
     */
    public function setLogger(Logger $logger);
}

