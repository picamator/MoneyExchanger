<?php
namespace Application\Model\Yahoo;

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
}

