<?php


namespace iHTML\Ccs;

use Sabberworm\CSS;
use Exception;
use Closure;

class CcsParser
{
    private Closure $onSelectorEvent;
    private Closure $onImportEvent;
    const   INHERITANCE_TREE = 3;
    const   INHERITANCE_LIST = 4;

    public function setOnImport(callable $onImport): self
    {
        $this->onImportEvent = Closure::fromCallable($onImport);
        return $this;
    }

    public function setOnSelector(callable $onSelector): self
    {
        $this->onSelectorEvent = Closure::fromCallable($onSelector);
        return $this;
    }

    public function parse(string $code, \Directory $root): self
    {
        $oCssParser = ( new CSS\Parser($code) )->parse();
        foreach ($oCssParser->getContents() as $oContent) {
            if ($oContent instanceof CSS\Property\Import) {
                ($this->onImportEvent)($oContent->atRuleArgs()[0]->getUrl()->getString(), $root);
            } elseif ($oContent instanceof CSS\RuleSet\DeclarationBlock) {
                if (empty($oContent->getRules())) {
                    continue;
                }
                // selectors_weight(...$oContent->getSelectors()); // TODO
                $rules = array_map(function ($oRule) use ($root) {
                    $name = $oRule->getRule();
                    $value = $oRule->getValue();
                    $valueList = $value instanceof CSS\Value\RuleValueList ? $value->getListComponents() : [$value];
                    foreach ($valueList as $valueElem) {
                        if ($valueElem instanceof CSS\Value\URL) {
                            $valueElem = new CSS\Value\CSSString(file_get_contents(working_dir($root->path, $valueElem->getURL()->getString())));
                        }
                        // else if var(--something)
                    }
                    if ($value instanceof CSS\Value\RuleValueList) {
                    } else {
                        $value = $valueList[0];
                    }
                    return new class($name, $value) {
                        public $name;
                        public $value;
                        public function __construct($name, $value)
                        {
                            $this->name  = $name;
                            $this->value = $value;
                        }
                    };
                }, $oContent->getRules());
                ($this->onSelectorEvent)(implode(',', $oContent->getSelectors()), $rules);
            } else {
                throw new Exception('Unexpected CSS element');
            }
        }
        return $this;
    }

    public function inheritanceFile(\SplFileObject $file): array
    {
        return $this->inheritanceCode($file->fread($file->getSize() + 1), dir($file->getPath()));
    }

    public function inheritanceCode(string $code, \Directory $root, int $style = self::INHERITANCE_LIST): array
    {
        $inheritance = [];
        $oCssParser = ( new CSS\Parser($code) )->parse();
        foreach ($oCssParser->getContents() as $oContent) {
            switch (true) {
                case $oContent instanceof CSS\Property\Import:
                    $import = $oContent->atRuleArgs()[0]->getUrl()->getString();
                    $imports = (new CcsParser)
                        ->setFile($oContent->atRuleArgs()[0]->getUrl()->getString())
                        ->inheritance();
                    if ($style === self::INHERITANCE_LIST) {
                        $inheritance = array_merge($inheritance, $imports);
                        $inheritance[] = $import;
                    }
                    if ($style === self::INHERITANCE_TREE) {
                        $inheritance[$import] = array_merge($hierarchy[$import], $imports);
                    }
                break;
            }
        }
        return $inheritance;
    }
}
