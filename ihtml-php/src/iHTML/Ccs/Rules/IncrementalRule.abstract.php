<?php

namespace iHTML\Ccs\Rules;

require_once dirname(__FILE__).'/BaseRule.abstract.php';

abstract class IncrementalRule extends BaseRule {

	protected static function constants(): array { return [
		'display' => \iHTML\Document\Modifiers\IncrementalModifier::DISPLAY,
		'content' => \iHTML\Document\Modifiers\IncrementalModifier::CONTENT,
		'none'    => \iHTML\Document\Modifiers\IncrementalModifier::NONE,
	]; }

	//function isValid(...$params): bool { return true; }

	protected static function solveValue($value, $dir)
	{
		$constants = static::constants();

		if($value instanceof \Sabberworm\CSS\Value\URL)

			return file_get_contents( working_dir($dir, $value->getURL()->getString()) );

		elseif($value instanceof \Sabberworm\CSS\Value\CSSString)

			return $value->getString();

		elseif(is_string($value) && isset($constants[ $value ]))
		
			return $constants[ $value ];

		else
		
			throw new \Exception('Value '.$value.' is not defined.');
	}
	
}


