<?php

namespace iHTML\Document\Modifiers;

class ContentModifier extends BaseModifier
{
    public function queryMethod(): string
    {
        return 'content';
    }
    
    public function isValid(...$params): bool
    {
        return true;
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
