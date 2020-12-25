<?php

namespace iHTML\Document\Modifiers;

require_once dirname(__FILE__).'/LateModifier.abstract.php';

class VisibilityModifier extends LateModifier
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

    public function defaultValue(): int
    {
        return self::VISIBLE;
    }

    public function render()
    {
        foreach ($this->lates as $late) {
            $late->element->parentNode->removeChild($late->element);
        }
    }
}
