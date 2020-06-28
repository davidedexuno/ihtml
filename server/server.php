#!/usr/bin/php
<?php

require('vendor/autoload.php');
require('../lib/iHTML.php');

$options = getopt('', ['project:', 'port:']);

// project
$project = new iHTML\Project($options['project']);

$port = isset($options['port']) && is_int($options['port']) ? $options['port'] : 1337;

$pathes = [
	'/' => true,
];
foreach($project->getTemplates() as $template)
	$pathes[ '/'.$template->output ] = [$template->html, $template->apply];

// build server

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket);
print 'Server loaded.'.PHP_EOL;

$http->on('request', function ($request, $response) use ($project, $pathes) {

    $headers = [
    	'Content-Type' => 'text/html',
  	];
  	$body = '404';


		$path = $request->getPath();


  	if(!isset( $pathes[ $path ])) {

		  $response->writeHead(404, $headers);
		  $response->end($body);
		  return;

  	}
  	// else:

		$resource = $pathes[ $path ];
		if($resource === true)
		{
			$body = '';

			$body .= <<<'EOD'
<html>
	<body>
		<style> ul, iframe {display:block;float:left;height:100%;} ul {width:200px;} iframe {width:1400px;}</style>
		<ul>
EOD;
			foreach($pathes as $path => $x)
				$body .= '<li><a href="'.$path.'" target="test">'.$path.'</a>';

			$body .= <<<'EOD'
		</ul>
		<iframe name="test" src="favicon.ico"></iframe>
	</body>
</html>
EOD;
		}

			print 'Rendered project page'."\n\n";

		else
		{
		
	  	list($html, $apply) = $resource;

			$document = new iHTML\Document(  working_dir($project->getRoot(), $html)  );
				$ccs = new iHTML\Ccs(  working_dir($project->getRoot(), $apply)  );
				$ccs->applyTo($document);
			$body = $document->render();

			print 'Rendered '.$path."\n\n";

		}

    $response->writeHead(200, $headers);
    $response->end($body);
	}
);

$socket->listen($port);
print 'Listening to http://127.0.0.1:'.$port.'...'.PHP_EOL.PHP_EOL;

$loop->run();

