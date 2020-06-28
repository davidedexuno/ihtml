<?php

namespace iHTML\Ccs\Rules;

require_once dirname(__FILE__).'/LateRule.abstract.php';

class VisibilityRule extends LateRule {

	static function rule(): string { return 'visibility'; }

	protected static function method(): string { return 'visibility'; }
	
	protected static function constants(): array { return [
		'visible' => \iHTML\Document\Modifiers\VisibilityModifier::VISIBLE,
		'hidden'  => \iHTML\Document\Modifiers\VisibilityModifier::HIDDEN,
	]; }

	//function isValid(...$params): bool { return in_array($params[0], [VisibilityRule::VISIBLE, VisibilityRule::HIDDEN]); }

}


