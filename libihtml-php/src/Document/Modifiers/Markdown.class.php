<?php

namespace iHTML\Document\Modifiers;

require_once dirname(__FILE__).'/Content.class.php';

class MarkdownModifier extends ContentModifier
{
    public function queryMethod(): string
    {
        return 'markdown';
    }
    
    public function apply(\DOMElement $element)
    {
        $content = static::solveParams($this->params, $element);
        
        $content = $this->parsedown->text($content);

        while ($element->hasChildNodes()) {
            $element->removeChild($element->firstChild);
        }
        if ($content) {
            $element->appendChild($this->domFragment($content));
        }
    }

    public $parsedown;
    
    public function __construct($domdocument)
    {
        parent::__construct($domdocument);
    
        $this->parsedown = new \Parsedown();
    }
}
