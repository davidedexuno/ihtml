<?php

namespace iHTML\Document;

use Symfony\Component\DomCrawler\Crawler;
use Exception;

class QueryJson
{
    private Crawler $nodelist;
    private Query $query;
    private string $name;

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
            $json = json_decode($entry->textContent);
            $this->setJsonByPath($json, $value);
            $entry->textContent = json_encode($json);
        }
        return $this->query;
    }
    
    public function display($value)
    {
        throw new Exception('json/display Not yet implemented.');
        return $this->query;
    }

    private function setJsonByPath($json, $value)
    {
        throw new Exception('json/set Not yet implemented.');
    }
}