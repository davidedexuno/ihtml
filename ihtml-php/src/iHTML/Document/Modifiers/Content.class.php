<?php

namespace iHTML\Document\Modifiers;

require_once dirname(__FILE__).'/IncrementalModifier.abstract.php';

class ContentModifier extends IncrementalModifier
{
    public function queryMethod(): string
    {
        return 'content';
    }
    
    public function apply(\DOMElement $element)
    {
        $content = static::solveParams($this->params, $element);

        while ($element->hasChildNodes()) {
            $element->removeChild($element->firstChild);
        }
        if ($content) {
            $element->appendChild($this->domFragment($content));
        }
    }
}
