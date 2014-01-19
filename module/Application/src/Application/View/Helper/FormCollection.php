<?php
/**
 * Wrapping form to <ul>
 */

namespace Application\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormCollection as BaseFormCollection;

class FormCollection extends BaseFormCollection 
{
    public function render(ElementInterface $element)
    {
        return '<dl>'.parent::render($element).'</dl>';
    }
}