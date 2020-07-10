<?php

namespace iHTML\Document;

use Symfony\Component\DomCrawler\Crawler;

class QueryAttribute
{
    private Crawler $nodelist;
    private Query $query;
    private string $name;

    // TODO:
    // const CONTENT = 2003;
    // const DISPLAY = 2004;
    // const NONE    = 2005;
    // function display($value)
    // const VISIBLE = 2006;
    // const HIDDEN  = 2007;
    // function visibility($value)

    public function __construct(Query $query, Crawler $nodelist, string $name)
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
            $entry->setAttribute($this->name, $value);
        }
        return $this->query;
    }
}
