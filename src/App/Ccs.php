<?php


namespace iHTML\Ccs;

use iHTML\Document\Document;
use iHTML\Document\DocumentQueryAttr;
use iHTML\Document\DocumentQueryClass;
use iHTML\Document\DocumentQueryStyle;
use Exception;
use SplFileObject;
use Directory;
use danog\ClassFinder\ClassFinder;
use CcsRuleDecoder;

class CcsFile extends CcsHandler
{
    public function __construct(SplFileObject $file)
    {
        parent::__construct();
        $this->code = $file->fread($file->getSize() + 1);
        $this->root = dir($file->getPath());
    }
}


class CcsChunk extends CcsHandler
{
    public function __construct(string $code, Directory $root)
    {
        parent::__construct();
        $this->code = $code;
        $this->root = $root;
    }
}


abstract class CcsHandler
{
    protected string $code;
    protected Directory $root;

    private $rules = [];
    private $attrRules = [];
    private $styleRules = [];
    private $classRules = [];

    public function __construct()
    {
        $this->loadRules();
        $this->loadAttrRules();
        $this->loadStyleRules();
        $this->loadClassRules();
    }


    public function applyTo(Document $document): CcsHandler
    {
        $parser =
            (new CcsParser)
                ->setOnSelector(function (string $selectors, array $rules) use ($document) {
                    $query = $document($selectors);
                    if (!iterator_count($query)) {
                        return;
                    }
                    foreach ($rules as $rule) {
                        $ruleComponents = CcsRuleDecoder::decodeRule($this->rules, $rule->name);
                        $ruleType = $ruleComponents->type;
                        $ruleName = $ruleComponents->rule;
                        $ruleSubj = $ruleComponents->name;
                        switch ($ruleType) {
                            case 'node':
                                $this->rules[ $ruleName ]::exec($query, $rule->values, $rule->content);
                            break;
                            case 'attr':
                                $this->attrRules[ $ruleName ]($query, $ruleSubj, $rule->values, $rule->content);
                            break;
                            case 'style':
                                $this->styleRules[ $ruleName ]($query, $ruleSubj, $rule->values, $rule->content);
                            break;
                            case 'class':
                                $this->classRules[ $ruleName ]($query, $ruleSubj, $rule->values, $rule->content);
                            break;
                            default:
                                throw new Exception("Rule type {$ruleType} not defined.");
                            break;
                        }
                    }
                })
                ->setOnImport(function (string $file) use ($document) {
                    $ccs = new CcsFile(new SplFileObject(working_dir($this->root->path, $file)));
                    $ccs->applyTo($document);
                })
                ->parse($this->code, $this->root);
        return $this;
    }


    public function getHierarchyList(): array
    {
        if ($this->file) {
            $parser = new CcsParser;
            return $parser->inheritanceFile($this->file, CcsParser::INHERITANCE_LIST);
        } elseif ($this->code) {
            $parser = new CcsParser;
            return $parser->inheritanceCode($this->code, CcsParser::INHERITANCE_LIST);
        } else {
            throw new Exception('Ccs: code or file not set');
        }
    }


    public function getHierarchyTree(): array
    {
        if ($this->file) {
            $parser = new CcsParser;
            return $parser->inheritanceFile($this->file, CcsParser::INHERITANCE_TREE);
        } elseif ($this->code) {
            $parser = new CcsParser;
            return $parser->inheritanceCode($this->code, CcsParser::INHERITANCE_TREE);
        } else {
            throw new Exception('Ccs: code or file not set');
        }
    }


    private function loadRules()
    {
        $this->rules = array_reduce(getClassesInNamespace('iHTML\Ccs\Rules'), function ($acc, $rule) {
            $acc[ $rule::rule() ] = $rule;
            return $acc;
        }, []);
    }

    private function loadAttrRules()
    {
        $this->attrRules = [
            'content' => function ($query, $name, $values, $content) {
                $values = $this->solveValues($values);
                $query->attr($name)->content(...$values);
            },
            'display' => function ($query, $name, $values, $content) {
                $values = $this->solveValues($values);
                $query->attr($name)->display(...$values);
            },
            'visibility' => function ($query, $name, $values, $content) {
                $values = $this->solveValues($values, ['visible' => DocumentQueryAttr::VISIBLE, 'hidden' => DocumentQueryAttr::HIDDEN]);
                $query->attr($name)->visibility(...$values);
            },
            // 'white-space' =>
        ];
    }

    private function loadStyleRules()
    {
        $this->styleRules = [
            'content' => function ($query, $name, $values, $content) {
                $values = $this->solveValues($values);
                $query->style($name)->content(...$values);
            },
            'literal' => function ($query, $name, $values, $content) {
                $query->style($name)->content($content);
            },
            'display' => function ($query, $name, $values, $content) {
                $values = $this->solveValues($values, ['none' => DocumentQueryStyle::NONE]);
                $query->style($name)->display(...$values);
            },
            'visibility' => function ($query, $name, $values, $content) {
                $values = $this->solveValues($values, ['visible' => DocumentQueryStyle::VISIBLE, 'hidden' => DocumentQueryStyle::HIDDEN]);
                $query->style($name)->visibility(...$values);
            },
            // 'white-space' =>
        ];
    }

    private function loadClassRules()
    {
        $this->classRules = [
            'visibility' => function ($query, $name, $values, $content) {
                $values = $this->solveValues($values, ['visible' => DocumentQueryClass::VISIBLE, 'hidden' => DocumentQueryClass::HIDDEN]);
                $query->className($name)->visibility(...$values);
            },
            // 'white-space' =>
        ];
    }

    private function solveValues($values, array $constants = [])
    {
        return array_map(function ($value) use ($constants) {
            if ($value instanceof \Sabberworm\Css\Value\CssString) {
                return $value->getString();
            } elseif (is_string($value) && isset($constants[ $value ])) {
                return $constants[ $value ];
            } else {
                throw new Exception("$value unrecognized");
            }
        }, $values);
    }
}
