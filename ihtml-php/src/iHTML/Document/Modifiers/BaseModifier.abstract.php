<?php

namespace iHTML\Document\Modifiers;

use Symfony\Component\DomCrawler\Crawler;

abstract class BaseModifier {

	protected $domdocument;
	
	protected $domlist;
	
	protected $params;

	abstract function queryMethod(): string;
	
	abstract function isValid(...$params): bool;
	
	abstract function apply(\DOMElement $element);

	function __construct(\DOMDocument $domdocument)
	{
	
		$this->domdocument = $domdocument;

	}

	function setList(Crawler $list)
	{
	
		$this->domlist = $list;
	
	}

	function __invoke(...$params)
	{

		if( !$this->isValid(...$params) )

			return; // or throw new Exception
		
		$this->params = $params;
		
		foreach( $this->domlist as $entry ) {

			$this->apply( $entry );

		}

	}
	
	function render()
	{

	}
	
	protected function domFragment($content)
	{
		$fragment = $this->domdocument->createDocumentFragment();
		//$fragment->appendXML($content);
		foreach(htmlToDOM($content, $this->domdocument) as $node)
			$fragment->appendChild($node);
		return $fragment;
	}
}


