<?php

namespace iHTML\Ccs\Rules;

require_once dirname(__FILE__).'/Content.class.php';

class ContentMarkdownRule extends ContentRule {

	static function rule(): string { return 'markdown'; }

	protected static function method(): string { return 'markdown'; }
	
}


