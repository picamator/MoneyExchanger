<?php
namespace Application\Model\Yahoo;

use Application\Model\Yahoo\Exception\RuntimeException;
use Zend\Log\Logger;

/**
 * Yahoo! Finance API currency convertor client
 * 
 * @link http://akrabat.com/development/objects-in-the-model-layer/
 */
class FinaceApiClient implements FinaceApiClientInterface 
{
    /**
     * Api resourse pattern
     */
    const RESOURCE_PATTERN = '/d/quotes?s=%s%s=X&f=l1n';
    
    /**
     * Config
     * 
     * @var array
     */
    protected $config;
   
    /**
     * Convertable currency name
     * 
     * @var string
     */
    protected $from;
    
    /**
     * Currency result name
     * 
     * @var string 
     */
    protected $to;
    
    /**
     * Logger
     * 
     * @var \Zend\Log\Logger  
     */
    protected $logger = null;
    
    /**
     * Curl Options
     * 
     * @var array
     */
    protected $curlOptions = array(
        CURLOPT_CONNECTTIMEOUT  => 5, // timeout on connect 
        CURLOPT_TIMEOUT         => 5 // timeout on response 
    );
            
    /**
     * @param array $config
     * @throws RuntimeException
     */
    public function __construct(array $config) 
    {
        if(!isset($config['endpoint'])) {
            throw new RuntimeException('Error: configuration faild. Yahoo! API endpoint does not set.');
        }
        
        // sets From via config
        if (isset($config['convert']['from'])) {
            $this->setFrom($config['convert']['from']);
        }
        
        // sets To via config
        if (isset($config['convert']['to'])) {
            $this->setTo($config['convert']['to']);
        }
        
        $this->config = $config;
    }
    
    /**
     * Sets From
     *   
     * @param string $from
     * @return self
     */
    public function setFrom($from)
    {
        $this->from = $from;
        
        return $this;
    }
    
    /**
     * Gets From
     *   
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }
    
    /**
     * Sets to
     * 
     * @param string $to
     * @return self
     */
    public function setTo($to)
    {
        $this->to = $to;
        
        return $this;
    }
    
    /**
     * Gets To
     *   
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }
    
    /**
     * Sets Curl Options
     * 
     * @param array $curlOptions
     * @return self
     */
    public function setCurlOptions(array $curlOptions) 
    {
        $this->curlOptions = array_merge($this->curlOptions , $curlOptions);
        
        return $this;
    }
    
    /**
     * Sets Logger
     * 
     * @param \Zend\Log\Logger $logger
     * @return self
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
        
        return $this;
    }
    
    /**
     * Gets currency course
     * 
     * @return float
     * @throws RuntimeException
     */
    public function getCourse() 
    {
        $url                = $this->getUrl();
        $responseRaw        = $this->getResponse($url);
        $responseParced     = str_getcsv($responseRaw, ',', '"');    
        if(!isset($responseParced[0]) && !is_numeric($responseParced[0])) {
             throw new RuntimeException('Error: response form Yahoo! API ['.$responseRaw.'] contains unsupported format');
        }
        
        return \floatval($responseParced[0]);
    }
    
    /**
     * Gets Response from Yahoo! Finance API
     * 
     * @param string $url
     * @return string
     * @throws RuntimeException
     */
    protected function getResponse($url)
    {
         // create curl resource
        $ch = \curl_init();
        
        // set url with resourse
        \curl_setopt($ch, CURLOPT_URL, $url);

        // set configurable curl options
        \curl_setopt_array($ch, $this->curlOptions);
        
        // get response string
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        // get response
        $response = curl_exec($ch);

        // handle curl errors
        if(\curl_errno($ch) !== 0) {
            throw new RuntimeException('Error: Impossible to get response from Yahoo! API ['.\curl_error($ch).']');
        }
        
        // close curl
        \curl_close($ch); 
        
        // log response
        $this->logDebugMsg('Yahoo! API response: '.$response);
        
        return $response;
    }
    
    /**
     * Gets Url resource
     * 
     * @param string $from
     * @param string $to
     * @return string
     */
    protected function getUrl()
    {        
        return $this->config['endpoint'].'/'.sprintf(self::RESOURCE_PATTERN, $this->from, $this->to);
    }
    
    /**
     * Write message to Logger with Debug priority
     * 
     * @param string $msg
     */
    protected function logDebugMsg($msg) 
    {
        if (!is_null($this->logger)) {
            $this->logger->debug($msg);
        }
    }
}
