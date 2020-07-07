<?php

namespace iHTML\Ccs\Rules;

require_once dirname(__FILE__).'/IncrementalRule.abstract.php';

class TextRule extends IncrementalRule {

	static function rule(): string { return 'text'; }

	protected static function method(): string { return 'text'; }
	
}


