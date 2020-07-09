<?php

namespace iHTML\Ccs\Rules;

require_once dirname(__FILE__).'/LateRule.abstract.php';

abstract class LateInheritedRule extends LateRule
{
    protected static function constants(): array
    {
        return [
        'inherit' => \iHTML\Document\Modifiers\LateInheritedModifier::INHERIT,
    ];
    }
}
