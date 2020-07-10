<?php

namespace iHTML\Document;

use Symfony\Component\DomCrawler\Crawler;

class QueryStyle
{
    private Crawler $nodelist;
    private Query $query;
    private string $name;

    const NONE    = 2005;
    // TODO:
    // const CONTENT = 2003;
    // const DISPLAY = 2004;
    //
    // const VISIBLE = 2006;
    // const HIDDEN  = 2007;
    // function visibility($value)

    public function __construct(Query $query, Crawler $nodelist, string $name)
    {
        $this->query    = $query;
        $this->nodelist = $nodelist;
        $this->name     = $name;
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
    
    public function __invoke($value)
    {
        return $this->content($value);
    }

    public function display($value)
    {
        foreach ($this->nodelist as $entry) {
            $rules = parse_style_attribute($entry->getAttribute('style'));
            if (isset($rules[ $this->name ])) {
                $old = render_style_attribute([ $this->name => $rules[ $this->name ] ]);
                $new = $value === self::NONE  ?  ''  :  trim($value, ';').';';
                $subject = render_style_attribute($rules);
                $style = str_replace($old, $new, $subject);
            } else {
                $style = render_style_attribute($rules) . $value;
            }
            $entry->setAttribute('style', $style);
        }
    
        return $this->query;
    }
}
