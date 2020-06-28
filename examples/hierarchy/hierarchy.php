#!/usr/bin/php
<?php

require(__DIR__.'/../../lib/iHTML.php');


print __DIR__.'/hierarchy.ccs'."\n";
$ccs = new iHTML\Ccs(__DIR__.'/hierarchy.ccs');
$hierarchyList = $ccs->getHierarchyList();
$hierarchyTree = $ccs->getHierarchyTree();

print_r($hierarchyList);

print_r($hierarchyTree);


