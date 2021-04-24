<?php

namespace iHTML\Document;

use Symfony\Component\DomCrawler\Crawler;

class DocumentQueryStyle
{
    private Crawler $nodelist;
    private DocumentQuery $query;
    private string $name;

    //const CONTENT = 2003;
    //const DISPLAY = 2004;
    const NONE    = 2005;
    const VISIBLE = 2006;
    const HIDDEN  = 2007;

    public function __construct(DocumentQuery $query, Crawler $nodelist, string $name)
    {
        $this->query    = $query;
        $this->nodelist = $nodelist;
        $this->name     = $name;
    }

    public function __invoke($value)
    {
        return $this->content($value);
    }

    public function content($value)
    {
        foreach ($this->nodelist as $entry) {
            $rules = parse_style_attribute($entry->getAttribute('style'));
            $rules[ $this->name ] = $value;
            $entry->setAttribute('style', render_style_attribute($rules));
        }
        return $this->query;
    }
    
    public function display($value)
    {
        $valueStyle = $value === self::NONE ? null : parse_style_attribute($value);
        foreach ($this->nodelist as $entry) {
            $rules = parse_style_attribute($entry->getAttribute('style'));
            if (isset($rules[ $this->name ])) {
                $newRules = [];
                foreach ($rules as $n => $v) {
                    if ($n == $this->name) {
                        if ($value === self::NONE) {
                        } else {
                            $newRules += $valueStyle;
                        }
                    } else {
                        $newRules[$n] = $rules[$n];
                    }
                }
                $rules = $newRules;
            } else {
                if ($value === self::NONE) {
                } else {
                    $rules += $valueStyle;
                }
            }
            $entry->setAttribute('style', render_style_attribute($rules));
        }
        return $this->query;
    }
}
