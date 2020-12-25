<?php

namespace iHTML\Document\Modifiers;

use Symfony\Component\DomCrawler\Crawler;

abstract class BaseModifier
{
    protected $domdocument;
    
    protected $domlist;
    
    protected $params;

    abstract public function queryMethod(): string;
    
    abstract public function isValid(...$params): bool;
    
    abstract public function apply(\DOMElement $element);

    public function __construct(\DOMDocument $domdocument)
    {
        $this->domdocument = $domdocument;
    }

    public function setList(Crawler $list)
    {
        $this->domlist = $list;
    }

    public function __invoke(...$params)
    {
        if (!$this->isValid(...$params)) {
            return;
        } // or throw new Exception
        
        $this->params = $params;
        
        foreach ($this->domlist as $entry) {
            $this->apply($entry);
        }
    }
    
    public function render()
    {
    }
    
    protected function domFragment($content)
    {
        $fragment = $this->domdocument->createDocumentFragment();
        //$fragment->appendXML($content);
        foreach (htmlToDOM($content, $this->domdocument) as $node) {
            $fragment->appendChild($node);
        }
        return $fragment;
    }
}
