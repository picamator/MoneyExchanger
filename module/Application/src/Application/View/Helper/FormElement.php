<?php
/**
 * Wrapping every form element to <li>
 */

namespace Application\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormElement as BaseFormElement;

class FormElement extends BaseFormElement 
{
    public function render(ElementInterface $element) 
    {      
        return sprintf('<dt>%s</dt>', parent::render($element));
    }
}