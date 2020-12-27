<?php


namespace iHTML\Ccs;

use Sabberworm\CSS;
use Exception;
use Closure;

class CcsParser
{
    private Closure $onSelectorEvent;
    const   INHERITANCE_TREE = 3;
    const   INHERITANCE_LIST = 4;

    public function parseFile(\SplFileObject $file)
    {
        $this->parseCode($file->fread($file->getSize() + 1), dir($file->getPath()));
    }

    public function parseCode(string $code, \Directory $dir)
    {
        $oCssParser = ( new CSS\Parser($code) )->parse();
        foreach ($oCssParser->getContents() as $oContent) {
            if ($oContent instanceof CSS\Property\Import) {
                (new CcsParser)
                    ->setOnSelector($this->onSelectorEvent)
                    ->parseFile( new \SplFileObject( working_dir($dir->path, $oContent->atRuleArgs()[0]->getUrl()->getString()) ) );
            } elseif ($oContent instanceof CSS\RuleSet\DeclarationBlock) {
                if (!empty($oContent->getRules())) {
                    continue;
                }
                // selectors_weight(...$oContent->getSelectors()); // TODO
                $rules = array_map(function ($oRule) {
                    $name = $oRule->getRule();
                    $value = $oRule->getValue();
                    $valueList = $value instanceof CSS\Value\RuleValueList ? $value->getListComponents() : [$value];
                    foreach ($valueList as $valueElem) {
                        if ($valueElem instanceof CSS\Value\URL) {
                            $valueElem = new CSS\Value\CSSString(file_get_contents(working_dir($dir->path, $valueElem->getURL()->getString())));
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

    public function inheritanceFile(\SplFileObject $file)
    {
        $this->inheritanceCode(file_get_contents($file), dirname($file));
    }

    public function inheritanceCode(string $code, \Directory $dir, int $style = self::INHERITANCE_LIST)
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

    public function setOnSelector(callable $onSelector)
    {
        $this->onSelectorEvent = Closure::fromCallable($onSelector);
        return $this;
    }
}
