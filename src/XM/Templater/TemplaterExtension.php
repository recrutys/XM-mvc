<?php

namespace XM\Templater;

class TemplaterExtension
{
	public function fnCss($templateName, $type = 'pub')
	{
		$containerLess = \XM::app()->container('local.lessFiles');

		\XM::app()->container()->offsetSet('local.lessFiles', array_merge($containerLess, [
			$templateName => strtolower($type)
		]));
	}

	public function fnDump($var)
	{
		dump($var);
	}
}