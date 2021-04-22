<?php

namespace iHTML\Ccs;

use Exception;
use iHTML\Document\DocumentModifier;


abstract class CcsRule
{

    // es: text-transform
    abstract public static function rule(): string;

    abstract public static function method(): string;

    //abstract function isValid(...$params): bool;
    
    public static function constants(): array
    {
        return [
            'display' => DocumentModifier::DISPLAY,
            'content' => DocumentModifier::CONTENT,
            'none'    => DocumentModifier::NONE,
            'inherit' => DocumentModifier::INHERIT,
        ];
    }

    public static function exec($query, $value)
    {
        $query->{ static::method() }(...static::solveValues($value));
    }

    protected static function solveValues($ruleValue/*, $dir*/)
    {
        $ruleValueList = $ruleValue instanceof \Sabberworm\CSS\Value\RuleValueList ? $ruleValue->getListComponents() : [ $ruleValue ];
        $ruleValueList = array_map(function ($element) {
            return $element;
        }, $ruleValueList);
        $ruleValueList = array_map(function ($value)/* use ($dir)*/ {
            return static::solveValue($value/*, $dir*/);
        }, $ruleValueList);
    
        return $ruleValueList;
    }

    protected static function solveValue($value/*, $dir*/)
    {
        $constants = static::constants();

        if ($value instanceof \Sabberworm\CSS\Value\CSSString) {
            return $value->getString();
        } elseif (is_string($value) && isset($constants[ $value ])) {
            return $constants[ $value ];
        } else {
            throw new Exception("Value $value is not defined.");
        }
    }
}
