<?php
/**
 * Form for currency converter caluclator
 */

namespace Application\Form;

use Zend\Form\Form;

class ConverterForm extends Form
{
    /**
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name = null, $options = array());
        
        $this->setAttribute('method', 'post');
             
        $this->add(array(
            'name' => 'convertible',
            'attributes' => array(
                'type'  => 'text',
                'id'    => 'convertible'
            )
        ));
             
        $this->add(array(
           'name' => 'submit',
           'type' => 'Submit',
           'attributes' => array(
               'id' => 'convert',
           )
       )); 
    }
}
