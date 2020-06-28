
<?php

namespace iHTML\Ccs\Rules;

require_once dirname(__FILE__).'/LateInheritedRule.abstract.php';

class TextTransformRule extends LateInheritedRule {

	static function rule(): string { return 'text-transform'; }

	protected static function method(): string { return 'textTransform'; }
	
	protected static function constants(): array { return
	parent::constants() + [
		'uppercase'  => \iHTML\Document\Modifiers\TextTransformModifier::UPPERCASE,
		'lowercase'  => \iHTML\Document\Modifiers\TextTransformModifier::LOWERCASE,
		'capitalize' => \iHTML\Document\Modifiers\TextTransformModifier::CAPITALIZE,
		'none'       => \iHTML\Document\Modifiers\TextTransformModifier::NONE,
	]; }

	//function isValid(...$params): bool { return in_array($params[0], [self::UPPERCASE, self::LOWERCASE, self::CAPITALIZE, self::INHERIT]); }

}


