<?php

namespace iHTML\Document;

use Symfony\Component\DomCrawler\Crawler;

class Query
{
	private $query; // => new Crawler

	function __construct(\DOMDocument $domdocument, array $modules, string $selector)
	{

		$this->query = (new Crawler($domdocument))->filter($selector);

		foreach( $modules as $moduleName => $module )
		{

			if( method_exists($this, $moduleName) )

				throw new Exception('Modifier name `'.$moduleName.'` is a reserved name.');

			$this->$moduleName = $module;
			
			$this->$moduleName->setList( $this->query );
			
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
	function __call(string $name, array $arguments)
	{
	
		($this->$name)(...$arguments);
	
		return $this;
		
	}

	function empty()
	{

		return count($this->query) == 0;

	}
	
	function getResults()
	{

		return $this->query;

	}

	function attr($name, $value = null)
	{
	
		if( func_num_args() == 1 )

			return new QueryAttribute($this, $this->query, $name);

		// else:

		( new QueryAttribute($this, $this->query, $name) )( $value );

		return $this;

	}

	function style($name, $value = null)
	{
	
		if(func_num_args() == 1)

			return new QueryStyle($this, $this->query, $name);

		// else:

		( new QueryStyle($this, $this->query, $name) )( $value );

		return $this;

	}

	function className($name, $value = null)
	{

		if(func_num_args() == 1)

			return new QueryClass($this, $this->query, $name);

		// else:

		( new QueryClass($this, $this->query, $name) )( $value );

		return $this;

	}

}


