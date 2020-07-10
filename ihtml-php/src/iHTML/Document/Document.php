<?php

namespace iHTML\Document;

use iHTML\Ccs\Ccs;

require_once(dirname(__FILE__).'/../Ccs/Ccs.php');

class Document
{
    private $domdocument;
    private $modifiers = [];

    public function __construct($html)
    {
        $html = realpath($html);
        if (!$html) {
            throw new \Exception('File `'.$html.'` not found.');
        }
        $this->domdocument = (new \Masterminds\HTML5)->load($html, [ \Masterminds\HTML5\Parser\DOMTreeBuilder::OPT_DISABLE_HTML_NS => true]);
        $this->loadModifiers();
        // LOAD INTERNAL CCS
        // <link rel="contentsheet" href="..."> ...
        foreach ($this('link[rel="contentsheet"][href]')->getResults() as $result) {
            $ccs = new Ccs(working_dir(dirname($html), $result->getAttribute('href')));
            $ccs->applyTo($this);
            $result->parentNode->removeChild($result);
        }
        // <dom> ... </dom> ...
        foreach ($this('content')->getResults() as $result) {
            $ccs = new Ccs();
            $ccs->setContent($result->textContent, dirname($html));
            $ccs->applyTo($this);
            $result->parentNode->removeChild($result);
        }
        // <ELEM dom="..."> ...
        foreach ($this('[content]')->getResults() as $result) {
            // TODO
        }
    }


    // implements $document('SELECTOR') ...
    public function __invoke($selector)
    {
        return new Query($this->domdocument, $this->modifiers, $selector);
    }


    // final rendering
    public function render($output = null)
    {
        // render modifiers final changes
        foreach ($this->modifiers as $modifier) {
            $modifier->render();
        }

        // returns / prints / writes document
        switch ($output) {
            case null:
                return (new \Masterminds\HTML5)->saveHTML($this->domdocument);
            break;
            case STDOUT:
                print (new \Masterminds\HTML5)->saveHTML($this->domdocument);
            break;
            default:
                $out_dir = dirname($output);
                if (!empty($out_dir) && !file_exists($out_dir)) {
                    mkdir($out_dir, 0777, true);
                }
    
                (new \Masterminds\HTML5)->save($this->domdocument, $output);
            break;
        }
    }
    
    private function loadModifiers()
    {
        foreach (glob(dirname(__FILE__).'/Modifiers/*.class.php') as $modifierFile) {
            require_once $modifierFile;
            
            $modifierName = str_replace([dirname(__FILE__).'/Modifiers/','.class.php'], '', $modifierFile);

            $class = 'iHTML\\Document\\Modifiers\\'.$modifierName.'Modifier';

            $modifier = new $class($this->domdocument);

            $this->modifiers[ $modifier->queryMethod() ] = $modifier;
        }
    }
}
