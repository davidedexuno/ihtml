<?php

class CcsRuleDecoder
{
    const CSS_RULES = ['align-content', 'align-items', 'align-self', 'all', 'animation', 'animation-delay', 'animation-direction', 'animation-duration', 'animation-fill-mode',
    'animation-iteration-count', 'animation-name', 'animation-play-state', 'animation-timing-function', 'backface-visibility', 'background', 'background-attachment',
    'background-blend-mode', 'background-clip', 'background-color', 'background-image', 'background-origin', 'background-position', 'background-repeat', 'background-size',
    'border', 'border-bottom', 'border-bottom-color', 'border-bottom-left-radius', 'border-bottom-right-radius', 'border-bottom-style', 'border-bottom-width',
    'border-collapse', 'border-color', 'border-image', 'border-image-outset', 'border-image-repeat', 'border-image-slice', 'border-image-source', 'border-image-width',
    'border-left', 'border-left-color', 'border-left-style', 'border-left-width', 'border-radius', 'border-right', 'border-right-color', 'border-right-style',
    'border-right-width', 'border-spacing', 'border-style', 'border-top', 'border-top-color', 'border-top-left-radius', 'border-top-right-radius', 'border-top-style',
    'border-top-width', 'border-width', 'bottom', 'box-shadow', 'box-sizing', 'caption-side', 'clear', 'clip', 'color', 'column-count', 'column-fill', 'column-gap',
    'column-rule', 'column-rule-color', 'column-rule-style', 'column-rule-width', 'column-span', 'column-width', 'columns', 'content', 'counter-increment', 'counter-reset',
    'cursor', 'direction', 'display', 'empty-cells', 'filter', 'flex', 'flex-basis', 'flex-direction', 'flex-flow', 'flex-grow', 'flex-shrink', 'flex-wrap', 'float', 'font',
    '@font-face', 'font-family', 'font-size', 'font-size-adjust', 'font-stretch', 'font-style', 'font-variant', 'font-weight', 'hanging-punctuation', 'height',
    'justify-content', '@keyframes', 'left', 'letter-spacing', 'line-height', 'list-style', 'list-style-image', 'list-style-position', 'list-style-type', 'margin',
    'margin-bottom', 'margin-left', 'margin-right', 'margin-top', 'max-height', 'max-width', '@media', 'min-height', 'min-width', 'nav-down', 'nav-index', 'nav-left',
    'nav-right', 'nav-up', 'opacity', 'order', 'outline', 'outline-color', 'outline-offset', 'outline-style', 'outline-width', 'overflow', 'overflow-x', 'overflow-y',
    'padding', 'padding-bottom', 'padding-left', 'padding-right', 'padding-top', 'page-break-after', 'page-break-before', 'page-break-inside', 'perspective',
    'perspective-origin', 'position', 'quotes', 'resize', 'right', 'tab-size', 'table-layout', 'text-align', 'text-align-last', 'text-decoration', 'text-decoration-color',
    'text-decoration-line', 'text-decoration-style', 'text-indent', 'text-justify', 'text-overflow', 'text-shadow', 'text-transform', 'top', 'transform', 'transform-origin',
    'transform-style', 'transition', 'transition-delay', 'transition-duration', 'transition-property', 'transition-timing-function', 'unicode-bidi', 'vertical-align',
    'visibility', 'white-space', 'width', 'word-break', 'word-spacing', 'word-wrap', 'z-index'];

    public static function decodeRule($rules, $rule)
    {
        $result = new class {
            public $type;
            public $name;
            public $rule;
        };
        if (isset($rules[ $rule ])) {
            $result->type = 'node';
            $result->rule = $rule;
            return $result;
        }
        $type = false;
        $prop = false;
        $name = preg_replace_callback_array([
            '/^((?<type>attr|class|style)-)?/i' => function ($match) use (&$type) {
                if (isset($match['type'])) {
                    $type = $match['type'];
                }
                return '';
            },
            '/(-(?<prop>visibility|content|display|literal))?$/i' => function ($match) use (&$prop) {
                if (isset($match['prop'])) {
                    $prop = $match['prop'];
                }
                return '';
            },
        ], $rule);
        if ($type == false) {
            $type = in_array($name, self::CSS_RULES) ? 'style' : 'class';
        }
        if ($prop == false) {
            $prop = ['style' => 'literal', 'class' => 'visibility', 'attr' => 'content'][$type];
        }
        $result->type = $type;
        $result->name = $name;
        $result->rule = $prop;
        return $result;
    }
}
