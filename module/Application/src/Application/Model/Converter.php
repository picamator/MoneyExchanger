<?php
namespace Application\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;  
use Zend\InputFilter\InputFilterInterface;       
use Zend\I18n\Translator\Translator;

class Converter implements InputFilterAwareInterface
{
    public $convertible;
    protected $inputFilter;           

    /**
     * Translator
     * 
     * @var \Zend\I18n\Translator\Translator  
     */
    protected $translator;
    
    /**
     * @param \Zend\I18n\Translator\Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }
    
    public function exchangeArray($data)
    {
        $this->convertible  = (isset($data['convertible']))  ? $data['convertible']  : null;
    }

    // Add content to this method:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'convertible',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name'      => 'Regex',
                        'options'   => array(
                            'pattern' => '/^[0-9]+\.?[0-9]+$/',
                            'messages' => array(
                                \Zend\Validator\Regex::NOT_MATCH => $this->translator->translate('Input value is not correct. Please set number and try again.')
                            )
                        )
                    ),
                    array(
                        'name' => 'notEmpty',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\NotEmpty::IS_EMPTY => $this->translator->translate('Value is required and can\'t be empty.'),
                                \Zend\Validator\NotEmpty::INVALID  => $this->translator->translate('Value is required and can\'t be empty.')
                            )
                        )
                    )
                )
            ))); 

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}