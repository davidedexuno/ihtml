<?php


function htmlToDOM($html, $doc)
{
    $html = '<div id="html-to-dom-input-wrapper">' . $html . '</div>';
    $libxml_use_internal_errors = libxml_use_internal_errors(true);
    $hdoc = DOMDocument::loadHTML($html);
    libxml_use_internal_errors($libxml_use_internal_errors);
    $child_array = array();
    try {
        $children = $hdoc->getElementById('html-to-dom-input-wrapper')->childNodes;
        foreach ($children as $child) {
            $child = $doc->importNode($child, true);
            array_push($child_array, $child);
        }
    } catch (Exception $ex) {
        error_log($ex->getMessage(), 0);
    }
    return $child_array;
}

function parse_style_attribute($style)
{
    $style = trim($style, " \t\n\r\0\x0B".';');
    if (!$style) {
        return [];
    }
    $style = explode(';', $style);
    $style = array_map(function ($rule) {
        return explode(':', $rule, 2);
    }, $style);
    $rules = [];
    foreach ($style as list($rule, $value)) {
        $rules[ trim($rule) ] = trim($value);
    }
    return $rules;
}

function render_style_attribute($rules)
{
    $style = '';
    foreach ($rules as $rule => $value) {
        $style .= "$rule:$value;";
    }
    return $style;
}

function working_dir(string $dir, string $file): string
{
    if ($file[0] == '/') { // is absolute
        return $file;
    }
    
    if (substr($file, 0, 7) == 'file://') { // is absolute, in format "file://"
        return $file;
    }

    return $dir . '/' . $file;
}

function array_usearch($needle, array $haystack, callable $callback)
{
    $res = array_filter($haystack, function ($var) use ($needle, $callback) {
        return $callback($var, $needle);
    });

    if (count($res) == 0) {
        return false;
    }

    return each($res)['key'];
}

const SELECTOR_NAME_REGEX = '-?[_a-zA-Z]+[_a-zA-Z0-9-]*';

function selector_weight(string $selector, bool $important = false)
{
    $important = $important ? 1 : 0; // !important
    $style    = 0; // STYLE (e.g. style="rules")
    $ids      = 0; // #LABEL (e.g. #elementId)
    $classes  = 0; // .LABEL, [LABEL] and :LABEL (e.g. .class , [attr="value"])
    $elements = 0; // LABEL (e.g. H1)

    // removing :not selector (useless)
    $selector = str_replace(':not(', '', $selector);

    // remove labels
    $selector = preg_replace(['/'.SELECTOR_NAME_REGEX.'/', '/\s/'], ['L', ' '], $selector);

    // parsing...
    for ($i = 0; $i < strlen($selector); $i++) {
        $token = $selector[ $i ];
        switch ($token) {
        
            case '#':      $ids++; $i++; break;
            case '.':  $classes++; $i++; break;
            case '[':  $classes++; $i = strpos($selector, ']', $i); break;
            case ':':  $classes++; $i++; break;
            case 'L': $elements++; break;
            case '*':
            case ')':
            default: break;
        
        }
    }
    
    return $important.'.'.$style.'.'.$ids.'.'.$classes.'.'.$elements;
}

function weight_compare_gt(string $weight1, string $weight2)
{
    return version_compare($weight1, $weight2, '>');
}

function selectors_weight(string ...$selectors)
{
    $selector_weight = '0.0.0.0.0'; // starts with lowest
    
    foreach ($selectors as $selector) {
        $current_weight = selector_weight($selector);
        
        if (weight_compare_gt($current_weight, $selector_weight)) {
            $selector_weight = $current_weight;
        }
    }

    return $selector_weight;
}

function getMimetype($file)
{
    return [
        'css' => 'text/css',
        'js' => 'text/javascript',
    ][ pathinfo($file, PATHINFO_EXTENSION) ]
    ?? mime_content_type($file);
}

function getClassesInNamespace($ns)
{
    return array_filter(get_declared_classes(), function ($class) use ($ns) {
        return strpos($class, $ns) === 0;
    });
}


interface Message
{
}

class ProjectDirectory implements Message
{
    public function __construct()
    {
    }
}
class CcsCode implements Message
{
    public function __construct()
    {
    }
}

class IhtmlFile extends SplFileInfo
{
    private string $rawpath;
    public function __construct(string $file_name)
    {
        $this->rawpath = $file_name;
        parent::__construct($file_name);
    }
    public function __toString()
    {
        return $this->rawpath;
    }
}
