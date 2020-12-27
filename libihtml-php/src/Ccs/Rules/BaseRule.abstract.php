<?php

namespace iHTML\Ccs\Rules;

use Exception;

abstract class BaseRule
{

    // es: text-transform
    abstract public static function rule(): string;

    public static function exec($query, $value)
    {
        $query->{ static::method() }(...static::solveValues($value));
    }

    //abstract function isValid(...$params): bool;
    
    protected static function solveValues($value, $dir)
    {
        //$ruleValueList = $ruleValue instanceof CSS\Value\RuleValueList ? $ruleValue->getListComponents() : [ $ruleValue ];
        /*$ruleValueList = array_map(function($element) {
            return $element;
        }, $ruleValueList);*/
        $values = array_map(function ($value) use ($dir) {
            return static::solveValue($value, $dir);
        }, $values);
    
        return $values;
    }

    protected static function solveValue($value, $dir)
    {
        $constants = static::constants();

        if (!isset($constants[ $value ])) {
            throw new Exception("Value $value is not defined.");
        }
        
        return $constants[ $value ];
    }
    
    protected static function constants(): array
    {
        return [];
    }
    
    abstract protected static function method(): string;
}
