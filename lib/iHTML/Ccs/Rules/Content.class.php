<?php

namespace iHTML\Ccs\Rules;

require_once dirname(__FILE__).'/IncrementalRule.abstract.php';

class ContentRule extends IncrementalRule {

	static function rule(): string { return 'content'; }

	protected static function method(): string { return 'content'; }
	
}


