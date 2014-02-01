<?php
namespace Application;

use Application\Model\Converter;

/**
 * Injection of Converter Model
 */
interface ConverterAwareInterface
{
    /**
     * Sets Converter Model
     * 
     * @param \Application\Model\Converter $converter
     */
    public function setConverter(Converter $converter);
}
