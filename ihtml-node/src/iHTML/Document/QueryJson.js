<?php

namespace iHTML\Document;

use Symfony\Component\DomCrawler\Crawler;

class QueryJson
{
    private Crawler $nodelist;
    private Query $query;
    private string $name;

    constructor(query, nodelist, name)
    {
        this.query    = query
        this.nodelist = nodelist
        this.name     = name
    }

    _call(value)
    {
        return this.content(value)
    }

    content(value)
    {
        for(entry of this.nodelist) {
            json = JSON.parse(entry.textContent)
            this.setJsonByPath(json, value)
            entry.textContent = JSON.strigify(json)
        }
        return this.query
    }
    
    display(value)
    {
        throw new Exception('json/display Not yet implemented.')
        return this.query
    }

    #setJsonByPath(json, value)
    {
        throw new Exception('json/set Not yet implemented.')
    }
}
