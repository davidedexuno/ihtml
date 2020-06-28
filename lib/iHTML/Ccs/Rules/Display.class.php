<?php

namespace iHTML\Ccs\Rules;

require_once dirname(__FILE__).'/IncrementalRule.abstract.php';

class DisplayRule extends IncrementalRule {

	static function rule(): string { return 'display'; }

	protected static function method(): string { return 'display'; }
	
}


