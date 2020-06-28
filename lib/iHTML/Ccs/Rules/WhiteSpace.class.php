<?php

namespace iHTML\Ccs\Rules;

require_once dirname(__FILE__).'/LateInheritedRule.abstract.php';

class WhiteSpaceRule extends LateInheritedRule {

	static function rule(): string { return 'white-space'; }

	protected static function method(): string { return 'whiteSpace'; }
	
	protected static function constants(): array { return
	parent::constants() + [
		'normal'   => \iHTML\Document\Modifiers\WhiteSpaceModifier::NORMAL,
		'nowrap'   => \iHTML\Document\Modifiers\WhiteSpaceModifier::NOWRAP,
		'pre'      => \iHTML\Document\Modifiers\WhiteSpaceModifier::PRE,
		'pre-line' => \iHTML\Document\Modifiers\WhiteSpaceModifier::PRELINE,
		'pre-wrap' => \iHTML\Document\Modifiers\WhiteSpaceModifier::PREWRAP,
	]; }

	//function isValid(...$params): bool { return in_array($params[0], [WhiteSpaceRule::NORMAL, WhiteSpaceRule::NOWRAP, WhiteSpaceRule::PRE, WhiteSpaceRule::PRELINE, WhiteSpaceRule::PREWRAP, WhiteSpaceRule::INHERIT]); }

}


