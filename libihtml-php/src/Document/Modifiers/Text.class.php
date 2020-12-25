<?php

namespace iHTML\Document\Modifiers;

require_once dirname(__FILE__).'/IncrementalModifier.abstract.php';

class TextModifier extends IncrementalModifier
{
    public function queryMethod(): string
    {
        return 'text';
    }
    
    public function apply(\DOMElement $element)
    {
        $content = static::solveParams($this->params, $element);

        $content = nl2br(htmlentities($content));

        while ($element->hasChildNodes()) {
            $element->removeChild($element->firstChild);
        }
        if ($content) {
            $element->appendChild($this->domFragment($content));
        }
    }
}
