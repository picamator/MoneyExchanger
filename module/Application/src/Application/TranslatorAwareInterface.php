<?php
namespace Application;

use Zend\I18n\Translator\Translator;

/**
 * Injection of Translator
 */
interface TranslatorAwareInterface
{
    /**
     * Sets Translator
     * 
     * @param \Zend\I18n\Translator\Translator $converter
     */
    public function setTranslator(Translator $translator);
}
