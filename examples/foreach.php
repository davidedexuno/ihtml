#!/usr/bin/php
<?php

require(__DIR__.'/../lib/iHTML.php');


$document = new \iHTML\Document( __DIR__.'/example.html');


// elements
$testforeach = [
	(object)array('prop1' => 'a', 'prop2' => 'b', 'prop3' => 'c'),
	(object)array('prop1' => 'd', 'prop2' => 'e', 'prop3' => 'f'),
	(object)array('prop1' => 'g', 'prop2' => 'h', 'prop3' => 'i'),
];
// foreach
foreach($testforeach as $each)
{
	// duplicate the template
	$document('.site-inner .content .entry-content:last-child')->display(\iHTML\Modifiers\DisplayModifier::DISPLAY, \iHTML\Modifiers\DisplayModifier::DISPLAY);

	$document('.site-inner .content .entry-content:nth-last-child(2) h2')->text($each->prop1);
	$document('.site-inner .content .entry-content:nth-last-child(2)')->attr('data-attr2')->content($each->prop2);
	$document('.site-inner .content .entry-content:nth-last-child(2) p')->content($each->prop3);
}
$document('.site-inner .content .entry-content:last-child')->display(\iHTML\Modifiers\DisplayModifier::NONE);


$document->render(STDOUT);


