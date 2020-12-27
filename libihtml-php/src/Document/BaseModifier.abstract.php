<?php

namespace iHTML\Document\Modifiers;

use Symfony\Component\DomCrawler\Crawler;

abstract class BaseModifier
{
    abstract public function queryMethod(): string;

    abstract public function isValid(...$params): bool;

    abstract public function apply(\DOMElement $element);

    protected $domdocument;
    protected $domlist;
    protected $params;

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
    
    const DISPLAY = 1001;
    const CONTENT = 1002;
    const NONE    = 1005;
    const INHERIT = 1012;

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

    protected static function solveParams(array $params, \DOMNode $entry): string
    {
        $content = [];
        foreach ($params as $param) {
            switch (true) {
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
                    foreach ($entry->childNodes as $childNode) {
                        $content[] = $entry->ownerDocument->saveHTML($childNode);
                    }
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

    public function applyLater(\DOMElement $element, $defaultValue)
    {
        $attribute = $this->params[0];

        // addElementToHierarchy

        // if exists, removes it
        if (($key = array_usearch($element, $this->lates, function ($a, $b) {
            return $a->element === $b;
        })) !== false) {
            array_splice($this->lates, $key, 1);
        }

        // if not default, adds it
        if ($attribute != $defaultValue) {
            $this->lates[] = new Late($element, $attribute);
        }
    }

    protected $lates = [];
    
    public function latesExpandInherits()
    {
        $oldLates = $this->lates;

        // sorting by depth (asc)
        usort($oldLates, function ($a, $b) {
            return substr_count($a->element->getNodePath(), '/') - substr_count($b->element->getNodePath(), '/');
        });

        // expand
        $this->lates = [];
        foreach ($oldLates as $oldLate) {
            // expand single element (apply to all children the prop)
            foreach (( new \Symfony\Component\DomCrawler\Crawler($oldLate->element) )->filter('*') as $childElement) {
                if (($key = array_usearch($childElement, $this->lates, function ($a, $b) {
                    return $a->element === $b;
                })) !== false) {
                    array_splice($this->lates, $key, 1);
                }

                $this->lates[] = new Late($childElement, $oldLate->attribute);
            }
        }
    }
}

class Late
{
    public $element;
    public $attribute;
    public function __construct($elem, $attr, int $weight = 0)
    {
        $this->element = $elem;
        $this->attribute = $attr;
        $this->weight = $weight;
    }
}
