<?php

namespace iHTML\Document;

use Symfony\Component\DomCrawler\Crawler;
use Exception;

class Query
{
    private Crawler $query;

    public function __construct(\DOMDocument $domdocument, array $modules, string $selector)
    {
        $this->query = (new Crawler($domdocument))->filter($selector);
        foreach ($modules as $moduleName => $module) {
            if (method_exists($this, $moduleName)) {
                throw new Exception("Modifier name '$moduleName' is a reserved name.");
            }
            $this->$moduleName = $module;
            $this->$moduleName->setList($this->query);
        }
    }
    
    //
    // To solve PHP syntax bug (and chaining).
    //
    // The code:
    // $this->display($a, $b, $c);
    //
    // with invokable objects, must be written as:
    //
    // ($this->display)($a, $b, $c);
    //
    public function __call(string $name, array $arguments)
    {
        ($this->$name)(...$arguments);
        return $this;
    }

    public function empty()
    {
        return count($this->query) == 0;
    }
    
    public function getResults()
    {
        return $this->query;
    }

    public function attr($name, $value = null)
    {
        if (func_num_args() == 1) {
            return new QueryAttribute($this, $this->query, $name);
        }
        ( new QueryAttribute($this, $this->query, $name) )($value);
        return $this;
    }

    public function style($name, $value = null)
    {
        if (func_num_args() == 1) {
            return new QueryStyle($this, $this->query, $name);
        }
        ( new QueryStyle($this, $this->query, $name) )($value);
        return $this;
    }

    public function className($name, $value = null)
    {
        if (func_num_args() == 1) {
            return new QueryClass($this, $this->query, $name);
        }
        ( new QueryClass($this, $this->query, $name) )($value);
        return $this;
    }
}
