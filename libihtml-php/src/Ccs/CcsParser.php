<?php


namespace iHTML\Ccs;



use Sabberworm\CSS;
use Exception;
use Closure;

class CcsParser
{
    private string  $file;
    private string  $code;
    private Closure $onSelectorEvent;
    const   INHERITANCE_TREE = 3;
    const   INHERITANCE_LIST = 4;

    public function setFile(string $file)
    {
        $this->file = $file;
        return $this;
    }

    public function setCode(string $code)
    {
        $this->code = $code;
        return $this;
    }

    public function onSelector(callable $onSelector)
    {
        $this->onSelectorEvent = Closure::fromCallable( $onSelector );
        return $this;
    }

    public function parse()
    {
        if($this->file) {
            $content = file_get_contents($this->file);
            $dir     = dirname(          $this->file);
        }
         else if($this->code) {
            $content = $this->code;
            $dir     = getcwd();
        }
        else {
            throw new Exception('Ccs parser: code or file not set');
        }
        $oCssParser = ( new CSS\Parser($content) )->parse();
        foreach ($oCssParser->getContents() as $oContent) {
            if($oContent instanceof CSS\Property\Import) {
                (new CcsParser)
                    ->setFile( $oContent->atRuleArgs()[0]->getUrl()->getString() )
                    ->onSelector( $this->onSelectorEvent )
                    ->parse();
            } else if($oContent instanceof CSS\RuleSet\DeclarationBlock) {
                if (!empty( $oContent->getRules() )) {
                    continue;
                }
                // selectors_weight(...$oContent->getSelectors()); // TODO
                $rules = array_map(function($oRule) {
                    $name = $oRule->getRule();
                    $value = $oRule->getValue();
                    $valueList = $value instanceof CSS\Value\RuleValueList ? $value->getListComponents() : [$value];
                    foreach($valueList as $valueElem)
                    {
                        if ($valueElem instanceof CSS\Value\URL) {
                            $valueElem = new CSS\Value\CSSString( file_get_contents( working_dir($dir, $valueElem->getURL()->getString()) ) );
                        }
                        // else if var(--something)
                    }
                    if($value instanceof CSS\Value\RuleValueList) {
                    } else {
                        $value = $valueList[0];
                    }
                    return new class($name, $value) {
                        public $name;
                        public $value;
                        function __construct($name, $value) {
                        	$this->name  = $name;
                        	$this->value = $value;
                        }
                    };
                }, $oContent->getRules());
                ($this->onSelectorEvent)( implode(',', $oContent->getSelectors()), $rules );
            } else {
                throw new Exception('Unexpected CSS element');
            }
        }
        return $this;
    }

    public function inheritance(int $style = self::INHERITANCE_LIST)
    {
        if($this->file) {
            $content = file_get_contents($this->file);
            $dir     = dirname(          $this->file);
        }
         else if($this->code) {
            $content = $this->code;
            $dir     = getcwd();
        }
        else {
            throw new Exception('Ccs parser: code or file not set');
        }
        $inheritance = [];
        $oCssParser = ( new CSS\Parser($content) )->parse();
        foreach ($oCssParser->getContents() as $oContent) {
            switch(true) {
                case $oContent instanceof CSS\Property\Import:
                    $import = $oContent->atRuleArgs()[0]->getUrl()->getString();
                    $imports = (new CcsParser)
                        ->setFile( $oContent->atRuleArgs()[0]->getUrl()->getString() )
                        ->inheritance();
                    if($style === self::INHERITANCE_LIST) {
                        $inheritance = array_merge($inheritance, $imports);
                        $inheritance[] = $import;
                    }
                    if($style === self::INHERITANCE_TREE) {
                        $inheritance[$import] = array_merge($hierarchy[$import], $imports);
                    }
                break;
            }
        }
        return $inheritance;
    }
}
