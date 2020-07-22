<?php


namespace iHTML\Ccs;

use iHTML\Document\Document;
use iHTML\Document\QueryAttr;
use iHTML\Document\QueryClass;
use iHTML\Document\QueryStyle;
use Exception;

interface CcsInterface
{
    public function __construct($file = null);

    public function setFile(string $file): self;

    public function setContent(string $content, string $root): self;

    public function getHierarchyList(): array;

    public function getHierarchyTree(): array;

    public function applyTo(Document $document): self;
}

class Ccs implements CcsInterface
{
    private $rules = [];
    private $attrRules = [];
    private $styleRules = [];
    private $classRules = [];
    private $rile = false;
    private $root = false;
    private $content = false;


    public function __construct($file = null)
    {
        $this->loadRules();
        $this->loadAttrRules();
        $this->loadStyleRules();
        $this->loadClassRules();
        if ($file != null) {
            $this->setFile($file);
        }
    }
    
    
    public function setFile(string $file): CcsInterface
    {
        if (!realpath($file)) {
            throw new Exception("File '$file' not found.");
        }
        $this->file = realpath($file);
        $this->setContent(file_get_contents($file), dirname($file));
        return $this;
    }


    public function setContent(string $content, string $root): CcsInterface
    {
        $this->root    = realpath($root);
        $this->content = $content;
        return $this;
    }


    public function getHierarchyList(): array
    {
        if ($this->content === false) {
            throw new Exception('Ccs Content not set.');
        }
        // useless check the root
        $hierarchy = [];
        $this->parseCcs($this->content, [
            'onImport' => function (string $import) use (&$hierarchy) {
                $ccs = new Ccs(working_dir($this->root, $import));
                $hierarchy = array_merge($hierarchy, $ccs->getHierarchyList());
            },
        ]);
        $hierarchy[] = $this->file;
        return $hierarchy;
    }


    public function getHierarchyTree(): array
    {
        if ($this->content === false) {
            throw new Exception('Ccs Content not set.');
        }
        // useless check the root
        $hierarchy = [$this->file => []];
        $this->parseCcs($this->content, [
            'onImport' => function (string $import) use (&$hierarchy) {
                $ccs = new Ccs(working_dir($this->root, $import));
                $hierarchy[$this->file] = array_merge($hierarchy[$this->file], $ccs->getHierarchyTree());
            },
        ]);
        return $hierarchy;
    }


    public function applyTo(Document $document): CcsInterface
    {
        if ($this->content === false) {
            throw new Exception('Ccs Content not set.');
        }
        // useless check the root
        $this->parseCcs($this->content, [
            'onImport' => function (string $import) use ($document) {
                $ccs = new Ccs(working_dir($this->root, $import));
                $ccs->applyTo($document);
            },
            'onSelector' => function (string $selector, array $rules) use ($document) {
                // if no rules
                if (empty($rules)) {
                    return;
                }
                $query = $document($selector);
                // if no affected nodes
                if ($query->empty()) {
                    return;
                }
                foreach ($rules as $rule) {
                    $rule->nameParsed = $this->loadRule($rule->name);
                    switch ($rule->nameParsed->type) {
                        case 'node':
                            $this->rules[ $rule->nameParsed->rule ]::exec($query, $rule->valueListed, $this->root);
                        break;
                        case 'attr':
                            $this->attrRules[ $rule->nameParsed->rule ]($query, $rule->nameParsed->name, $rule->valueListed);
                        break;
                        case 'style':
                            $this->styleRules[ $rule->nameParsed->rule ]($query, $rule->nameParsed->name, $rule->valueListed, $rule->value);
                        break;
                        case 'class':
                            $this->classRules[ $rule->nameParsed->rule ]($query, $rule->nameParsed->name, $rule->valueListed);
                        break;
                        default:
                            throw new Exception("Rule type {$rule->nameParsed->type} not defined.");
                        break;
                    }
                }
            },
        ]);
        return $this;
    }
    
    private function loadRules()
    {
        foreach (glob(dirname(__FILE__).'/Rules/*.class.php') as $ruleFile) {
            require_once $ruleFile;
            $ruleName = str_replace([dirname(__FILE__).'/Rules/','.class.php'], '', $ruleFile);
            $className = 'iHTML\\Ccs\\Rules\\'.$ruleName.'Rule';
            if (!class_exists($className)) {
                throw new Exception("Class $className doesn't exists.");
            }
            $this->rules[ $className::rule() ] = $className;
        }
    }

    private function loadAttrRules()
    {
        $this->attrRules = [
            'content' => function ($query, $name, $values) {
                $values = $this->solveValues($values);
                $query->attr($name)->content(...$values);
            },
            'display' => function ($query, $name, $values) {
                $values = $this->solveValues($values);
                $query->attr($name)->display(...$values);
            },
            'visibility' => function ($query, $name, $values) {
                $values = $this->solveValues($values, ['visible' => QueryAttr::VISIBLE, 'hidden' => QueryAttr::HIDDEN]);
                $query->attr($name)->visibility(...$values);
            },
            // 'white-space' =>
        ];
    }

    private function loadStyleRules()
    {
        $this->styleRules = [
            'content' => function ($query, $name, $values, $value) {
                $values = $this->solveValues($values);
                $query->style($name)->content(...$values);
            },
            'literal' => function ($query, $name, $values, $value) {
                $query->style($name)->content((string)$value);
            },
            'display' => function ($query, $name, $values, $value) {
                $values = $this->solveValues($values, ['none' => QueryStyle::NONE]);
                $query->style($name)->display(...$values);
            },
            'visibility' => function ($query, $name, $values, $value) {
                $values = $this->solveValues($values, ['visible' => QueryStyle::VISIBLE, 'hidden' => QueryStyle::HIDDEN]);
                $query->style($name)->visibility(...$values);
            },
            // 'white-space' =>
        ];
    }

    private function loadClassRules()
    {
        $this->classRules = [
            'visibility' => function ($query, $name, $values) {
                $values = $this->solveValues($values, ['visible' => QueryClass::VISIBLE, 'hidden' => QueryClass::HIDDEN]);
                $query->className($name)->visibility(...$values);
            },
            // 'white-space' =>
        ];
    }

    private function solveValues($values, array $constants = [])
    {
        return array_map(function ($value) use ($constants) {
            if ($value instanceof \Sabberworm\CSS\Value\CSSString) {
                return $value->getString();
            } elseif (is_string($value) && isset($constants[ $value ])) {
                return $constants[ $value ];
            } else {
                throw new Exception("$value unrecognized");
            }
        }, $values);
    }


    private function loadRule($rule)
    {
        $result = new class {
            public $type;
            public $name;
            public $rule;
        };
        if (isset($this->rules[ $rule ])) {
            $result->type = 'node';
            $result->rule = $rule;
            return $result;
        }
        $types = implode('|', ['attr', 'class', 'style']);
        $props = implode('|', ['visibility', 'content', 'display', 'literal']);
        $type = false;
        $prop = false;
        $name = preg_replace_callback_array([
            '/^((?<type>'.$types.')-)?/i' => function ($match) use (&$type) {
                if (isset($match['type'])) {
                    $type = $match['type'];
                }
                return '';
            },
            '/(-(?<prop>'.$props.'))?$/i' => function ($match) use (&$prop) {
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


    private function parseCcs(string $content, array $handler)
    {
        $oCssParser = ( new \Sabberworm\CSS\Parser($content) )->parse();
        foreach ($oCssParser->getContents() as $oContent) {
            if ($oContent instanceof \Sabberworm\CSS\Property\Import) {
                if (isset($handler['onImport'])) {
                    $handler['onImport']($oContent->atRuleArgs()[0]->getUrl()->getString());
                }
            } elseif ($oContent instanceof \Sabberworm\CSS\RuleSet\DeclarationBlock) {
                // selectors_weight(...$oContent->getSelectors()); // TODO
                $selector = implode(',', $oContent->getSelectors());
                $rules = [];
                foreach ($oContent->getRules() as $oRule) {
                    $ruleName = $oRule->getRule();
                    $ruleValue = $oRule->getValue();
                    $rules[] = (object)array(
                        'name' => $ruleName,
                        'value' => $ruleValue,
                        'valueListed' => ($ruleValue instanceof \Sabberworm\CSS\Value\RuleValueList) ? $ruleValue->getListComponents() : [ $ruleValue ],
                    );
                }
                if (isset($handler['onSelector'])) {
                    $handler['onSelector']($selector, $rules);
                }
            }
        }
    }
}
