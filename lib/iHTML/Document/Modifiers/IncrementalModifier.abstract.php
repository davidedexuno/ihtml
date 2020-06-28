<?php

namespace iHTML\Document\Modifiers;

require_once dirname(__FILE__).'/BaseModifier.abstract.php';

abstract class IncrementalModifier extends BaseModifier {

	function isValid(...$params): bool { return true; }

	const DISPLAY = 1001;
	const CONTENT = 1002;
	const NONE    = 1005;

	protected static function solveParams(array $params, \DOMNode $entry): string
	{
		$content = [];
		foreach($params as $param) {
			switch(true) {

				case is_string($param):

					$content[] = $param;

				break;
				case $param === self::NONE:

					// none

				break;
				case $param === self::DISPLAY:

					$content[] = $entry->ownerDocument->saveHTML($entry);

				break;
				case $param === self::CONTENT:

					foreach($entry->childNodes as $childNode)
						$content[] = $entry->ownerDocument->saveHTML($childNode);

				break;
				case $param === self::TEXT:

					// TODO

				break;
				case $param instanceof ATTR and $param->value === self::CONTENT:

					$param = $entry->getAttribute($c->name);

				break;
				//case $param instanceof ATTR and $param->value === self::DISPLAY:
				
					// TODO

				//break;
				//case $param instanceof STYLE and $param->value === self::CONTENT:

					// TODO

				//break;
				//case $param instanceof STYLE and $param->value === self::DISPLAY:

					// TODO

				//break;

			}
		}
		return implode($content);
	}

}


