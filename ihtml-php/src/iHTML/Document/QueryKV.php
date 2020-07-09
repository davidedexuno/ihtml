<?php

namespace iHTML\Document;

use Symfony\Component\DomCrawler\Crawler;

class QueryElement
{
    protected $nodelist;
    protected $query;
    protected $name;

    public function __construct(Query $query, Crawler $nodelist, string $name)
    {
        $this->query    = $query;
        $this->nodelist = $nodelist;
        $this->name     = $name;
    }
}

class QueryAttribute extends QueryElement
{

    // TODO:
    // const CONTENT = 2003;
    // const DISPLAY = 2004;
    // const NONE    = 2005;
    // function display($value)
    // const VISIBLE = 2006;
    // const HIDDEN  = 2007;
    // function visibility($value)

    public function content($value)
    {
        foreach ($this->nodelist as $entry) {
            $entry->setAttribute($this->name, $value);
        }

        return $this->query;
    }
    
    public function __invoke($value)
    {
        return $this->content($value);
    }
}

class QueryClass extends QueryElement
{
    const VISIBLE = 3001;
    const HIDDEN  = 3002;

    public function visibility($value)
    {
        foreach ($this->nodelist as $entry) {
            $classes = preg_split('/\s+/', $entry->getAttribute('class'));

            if ($value === self::HIDDEN) {
                if (($i = array_search($this->name, $classes)) !== false) {
                    unset($classes[ $i ]);
                }
            }
            if ($value === self::VISIBLE) {
                if (!in_array($this->name, $classes)) {
                    $classes[] = $this->name;
                }
            }

            $entry->setAttribute('class', implode(' ', $classes));
        }
    
        return $this->query;
    }
    
    public function __invoke($value)
    {
        return $this->visibility($value);
    }
}

class QueryStyle extends QueryElement
{
    const NONE    = 2005;
    // TODO:
    // const CONTENT = 2003;
    // const DISPLAY = 2004;
    //
    // const VISIBLE = 2006;
    // const HIDDEN  = 2007;
    // function visibility($value)

    public function content($value)
    {

        /* act on $this->nodelist */
        foreach ($this->nodelist as $entry) {
            $rules = parse_style_attribute($entry->getAttribute('style'));

            $rules[ $this->name ] = $value;

            $entry->setAttribute('style', render_style_attribute($rules));
        }
    
        return $this->query;
    }
    
    public function display($value)
    {

        /* act on $this->nodelist */
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
    
    public function __invoke($value)
    {
        return $this->content($value);
    }
}
