<?php

namespace iHTML\Document\Modifiers;

require_once dirname(__FILE__).'/IncrementalModifier.abstract.php';

class DisplayModifier extends IncrementalModifier
{
    public function queryMethod(): string
    {
        return 'display';
    }
    
    public function apply(\DOMElement $element)
    {
        $content = static::solveParams($this->params, $element);

        $element->parentNode->replaceChild($this->domFragment($content), $element);
    }
}
