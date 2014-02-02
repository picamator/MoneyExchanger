<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Application\ConverterAwareInterface; 
use Application\Model\Converter; 
use Application\Form\ConverterForm; 

use Application\TranslatorAwareInterface;
use Zend\I18n\Translator\Translator;

use Application\YahooClientAwareInterface;
use Application\Model\Yahoo\FinaceApiClientInterface;

class IndexController extends AbstractActionController implements ConverterAwareInterface, 
        TranslatorAwareInterface, YahooClientAwareInterface
{
    /**
     * Converter Model
     * 
     * @var \Application\Model\Converter  
     */
    protected $converter;
    
    /**
     * Translator
     * 
     * @var \Zend\I18n\Translator\Translator  
     */
    protected $translator;
    
    /**
     * Yahoo! Finance Client
     * 
     * @var \Application\Model\Yahoo\FinaceApiClientInterface
     */
    protected $yahooClient;
    
    /**
     * Sets Converter Model
     * 
     * @param \Application\Model\Converter $converter
     */
    public function setConverter(Converter $converter)
    {
        $this->converter = $converter;
    }
    
    /**
     * Sets Translator
     * 
     * @param \Zend\I18n\Translator\Translator $converter
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }
    
    /**
     * Sets Yahoo! Finance API Client
     * 
     * @param \Application\Model\Yahoo\FinaceApiClientInterface $yahooClient
     */
    public function setYahooClient(FinaceApiClientInterface $yahooClient)
    {
        $this->yahooClient = $yahooClient;
    }
    
    /**
     * Display converter form
     * 
     * @return ViewModel
     */
    public function indexAction()
    {        
        $form = new ConverterForm();
        $form->get('submit')->setValue('Convert to PLN');
        $form->get('convertible')->setLabel('RUB');
                        
        return new ViewModel(array('form' => $form));
    }
    
    /**
     * Handler for converter form
     * Ajax request
     * 
     * @return JsonModel
     */
    public function convertAction()
    {
        $form     = new ConverterForm();
        // default error msg
        $response = array(
            'success'   => false,
            'msg'       => $this->translator->translate('Resource is avalible for calculator access only.')
        );
        
        $request = $this->getRequest();
        if ($request->isPost() && $request->isXmlHttpRequest()) {
            $form->setInputFilter($this->converter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $course = $this->yahooClient->getCourse();
                
                $response = array(
                    'success'   => true,
                    'course'    => array(
                        'from'  => $this->yahooClient->getFrom(),
                        'to'    => $this->yahooClient->getTo(),
                        'value' => $course
                    )
                );
            } else {
                $response = array(
                    'msg'       => implode($form->getMessages('convertible'), '<br />'),
                    'success'   => false
                );
            }
        }
               
        return new JsonModel($response);
    }
}