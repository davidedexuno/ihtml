<?php

namespace iHTML\Document\Modifiers;

require_once dirname(__FILE__).'/Content.class.php';

class BbcodeModifier extends ContentModifier {

	function queryMethod(): string { return 'bbcode'; }
	
	function apply(\DOMElement $element)
	{

		$content = static::solveParams($this->params, $element);
		
		$content = $this->parser->parse($content)->getAsHtml();

		while($element->hasChildNodes())
			$element->removeChild( $element->firstChild );
		if($content) $element->appendChild( $this->domFragment($content) );

	}

	public $parser;
	
	function __construct($domdocument)
	{
		parent::__construct($domdocument);
	
		$this->parser = new \JBBCode\Parser();
		$this->parser->addCodeDefinitionSet( new \JBBCode\DefaultCodeDefinitionSet() );
	
	}

}


