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

        if ($value instanceof \Sabberworm\CSS\Value\CSSString) {
            return $value->getString();
        } elseif (is_string($value) && isset($constants[ $value ])) {
            return $constants[ $value ];
        } else {
            throw new Exception("Value $value is not defined.");
        }
    }
    
    public static function constants(): array
    {
        return [
            'display' => \iHTML\Document\Modifiers\BaseModifier::DISPLAY,
            'content' => \iHTML\Document\Modifiers\BaseModifier::CONTENT,
            'none'    => \iHTML\Document\Modifiers\BaseModifier::NONE,
            'inherit' => \iHTML\Document\Modifiers\BaseModifier::INHERIT,
        ];
    }
    
    abstract public static function method(): string;
}
