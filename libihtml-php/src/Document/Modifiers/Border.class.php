<?php

namespace iHTML\Document\Modifiers;

class BorderModifier extends BaseModifier
{
    public function queryMethod(): string
    {
        return 'border';
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
