<?php

namespace iHTML\Document\Modifiers;

require_once dirname(__FILE__).'/BaseModifier.abstract.php';

abstract class LateModifier extends BaseModifier {

	protected $lates = [];
	
	function apply(\DOMElement $element)
	{
		$attribute = $this->params[0];

		// addElementToHierarchy

		// if exists, removes it
		if( ( $key = array_usearch($element, $this->lates, function($a, $b) { return $a->element === $b; } ) ) !== FALSE )

			array_splice($this->lates, $key, 1);

		// if not default, adds it
		if( $attribute != $this->defaultValue() )

			$this->lates[] = new Late($element, $attribute);

	}

	abstract function defaultValue(): int;

}

class Late
{
	public $element;
	public $attribute;
	
	public function __construct($elem, $attr, $weight = 0)
	{

		$this->element = $elem;
		$this->attribute = $attr;
		$this->weight = $weight;

	}
}


