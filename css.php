<?php

use XM\Mvc\Less;

require(__DIR__ . '/src/XM.php');

XM::start(__DIR__);

$request = XM::app()->request();
$response = XM::app()->response();

$compiledTemplates = [];

if (
	count($request->filter('template')) == count($request->filter('type'))
)
{
	foreach ($request->filter('template') as $key => $template)
	{
		$compiledTemplates[$template] = $request->filter('type')[$key];
	}
}

$compile = new Less($compiledTemplates);
$css = $compile->compile();

// Show
$response->body($css);
$response->contentType('text/css', 'utf-8');
$response->send();