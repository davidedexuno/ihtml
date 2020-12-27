<?php

namespace iHTML\Document\Modifiers;

class VisibilityModifier extends BaseModifier
{
    const VISIBLE = 1003;
    const HIDDEN  = 1004;

    public function queryMethod(): string
    {
        return 'visibility';
    }
    
    public function isValid(...$params): bool
    {
        return in_array($params[0], [self::VISIBLE, self::HIDDEN]);
    }

    public function apply(\DOMElement $element)
    {
        $this->applyLater($element, self::VISIBLE);
    }

    public function render()
    {
        foreach ($this->lates as $late) {
            $late->element->parentNode->removeChild($late->element);
        }
    }
}
