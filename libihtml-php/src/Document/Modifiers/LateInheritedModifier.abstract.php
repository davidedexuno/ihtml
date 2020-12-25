<?php

namespace iHTML\Document\Modifiers;

require_once dirname(__FILE__).'/LateModifier.abstract.php';

abstract class LateInheritedModifier extends LateModifier
{
    const INHERIT = 1012; // Inherits this property from its parent element. Read about inherit

    public function defaultValue(): int
    {
        return self::INHERIT;
    }

    public function render()
    {
        $oldLates = $this->lates;

        // sorting by depth (asc)
        usort($oldLates, function ($a, $b) {
            return substr_count($a->element->getNodePath(), '/') - substr_count($b->element->getNodePath(), '/');
        });

        // expand
        $this->lates = [];
        foreach ($oldLates as $oldLate) {
            // expand single element (apply to all children the prop)
            foreach (( new \Symfony\Component\DomCrawler\Crawler($oldLate->element) )->filter('*') as $childElement) {
                if (($key = array_usearch($childElement, $this->lates, function ($a, $b) {
                    return $a->element === $b;
                })) !== false) {
                    array_splice($this->lates, $key, 1);
                }

                $this->lates[] = new Late($childElement, $oldLate->attribute);
            }
        }
    }
}
