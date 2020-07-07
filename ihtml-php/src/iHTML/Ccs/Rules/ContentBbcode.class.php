<?php

namespace iHTML\Ccs\Rules;

require_once dirname(__FILE__).'/Content.class.php';

class ContentBbcodeRule extends ContentRule {

	static function rule(): string { return 'bbcode'; }

	protected static function method(): string { return 'bbcode'; }
	
}


