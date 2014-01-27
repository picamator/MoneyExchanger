<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Application\Model\Converter; 
use Application\Form\ConverterForm; 

class IndexController extends AbstractActionController
{
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
        $form       = new ConverterForm();
        $response   = array('data' => array(
            'success'   => true,
            'msg'       => 'ok',
            'course'    => array('from' => 'RUB', 'to' => 'PLZ', 'value' => 1.2))
        );
        
        /*$request = $this->getRequest();
        if ($request->isPost() && $request->isXmlHttpRequest()) {
            $converter = new Converter();
            $form->setInputFilter($converter->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                // @todo
            } else {
                // @todo
            }
        }*/
       
        return new JsonModel($response);
    }
}