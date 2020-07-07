<?php

namespace iHTML\Ccs\Rules;

require_once dirname(__FILE__).'/IncrementalRule.abstract.php';

class BorderRule extends IncrementalRule {

	static function rule(): string { return 'border'; }

	protected static function method(): string { return 'border'; }
	
}


