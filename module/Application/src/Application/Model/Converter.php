<?php
namespace Application\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;  
use Zend\InputFilter\InputFilterInterface;       

class Converter implements InputFilterAwareInterface
{
    public $convertible;
    protected $inputFilter;           

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
                    array('name' => 'Float'),
                ),
            ))); 

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}