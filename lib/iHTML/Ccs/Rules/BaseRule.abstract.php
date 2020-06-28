<?php

namespace iHTML\Ccs\Rules;

abstract class BaseRule {

	// es: text-transform
	abstract static function rule(): string;

	static function exec($query, $values, $dir)
	{
	
		$values = static::solveValues( $values, $dir );
	
		static::execute($query, $values, $dir);

	}

	//abstract function isValid(...$params): bool;
	
	protected static function solveValues($values, $dir)
	{
	
		$values = array_map(function($value) use ($dir) { return static::solveValue( $value, $dir ); }, $values);
	
		return $values;
		
	}

	protected static function solveValue($value, $dir)
	{

		$constants = static::constants();

		if( !isset($constants[ $value ]) ) throw new \Exception('Value '.$value.' is not defined.');
		
		return $constants[ $value ];

	}
	
	protected static function constants(): array { return []; }
	
	static function execute($query, $values, $dir)
	{
	
		$query->{ static::method() }( ...$values );

	}

	abstract protected static function method(): string;
	
}

