<?php
namespace Application;

use Application\Model\Yahoo\FinaceApiClientInterface;

/**
 * Injection of Yahoo! Converter Model
 */
interface YahooClientAwareInterface
{
    /**
     * Sets Yahoo! Finance API Client
     * 
     * @param \Application\Model\Yahoo\FinaceApiClientInterface $yahooClient
     */
    public function setYahooClient(FinaceApiClientInterface $yahooClient);
}
