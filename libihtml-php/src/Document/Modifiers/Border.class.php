<?php

namespace iHTML\Document\Modifiers;

require_once dirname(__FILE__).'/IncrementalModifier.abstract.php';

class BorderModifier extends IncrementalModifier
{
    public function queryMethod(): string
    {
        return 'border';
    }
    
    public function apply(\DOMElement $element)
    {
        $content = static::solveParams($this->params, $element);

        $element->parentNode->replaceChild($this->domFragment($content), $element);
    }
}
