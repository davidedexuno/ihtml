<?php

namespace iHTML\Document\Modifiers;

require_once dirname(__FILE__).'/LateInheritedModifier.abstract.php';

class TextTransformModifier extends LateInheritedModifier {

	const LOWERCASE  = 1013;
	const UPPERCASE  = 1014;
	const CAPITALIZE = 1015;
	const NONE       = 1016;

	function queryMethod(): string { return 'textTransform'; }
	
	function isValid(...$params): bool { return in_array($params[0], [self::UPPERCASE, self::LOWERCASE, self::CAPITALIZE, self::INHERIT]); }

	function render()
	{
		parent::render();

		$transforms = [
			self::LOWERCASE  => 'strtolower',
			self::UPPERCASE  => 'strtoupper',
			self::CAPITALIZE => 'ucwords',
		];
		
		foreach($this->lates as $late)
		{
			if($late->attribute == self::NONE)
				continue;

			// replace in all text child nodes
			for($i = 0; $i < $late->element->childNodes->length; $i++)
			{
				$childNode = $late->element->childNodes[ $i ];
				if($childNode instanceof \DOMText)
				{
					$text = $transforms[$late->attribute]($childNode->wholeText);
					$late->element->replaceChild( $late->element->ownerDocument->createTextNode($text), $childNode);
				}
			}
		}

	}
}


