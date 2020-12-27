<?php

namespace iHTML\Document\Modifiers;

class DisplayModifier extends BaseModifier
{
    public function queryMethod(): string
    {
        return 'display';
    }
    
    public function isValid(...$params): bool
    {
        return true;
    }

    public function apply(\DOMElement $element)
    {
        $content = static::solveParams($this->params, $element);

        $element->parentNode->replaceChild($this->domFragment($content), $element);
    }
}
