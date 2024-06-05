<?php

namespace XM\Twig;

use Twig\TwigFilter;
use XM;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TemplaterExtension extends AbstractExtension
{
	public array $functions = [
		'phrase'    => 'fnGetPhrase',
		'dump'      => 'fnDump',
		'getOption' => 'fnGetOption',
		'title'     => 'fnTitle',
		'css_src'   => 'fnCssSrc',
		'link'      => 'fnLink'
	];

	public array $filters = [
		'get' => 'filterGetEntity'
	];

	public function getFunctions(): array
	{
		$extensions = [];

		foreach ($this->functions as $key => $functionName)
		{
			$extensions[] = new TwigFunction($key, [$this, $functionName]);
		}

		return $extensions;
	}

	public function getFilters(): array
	{
		$filters = [];

		foreach ($this->filters as $filter => $function)
		{
			$filters[] = new TwigFilter($filter, [$this, $function]);
		}

		return $filters;
	}

	public function fnGetPhrase($phraseName, $params = []): string
	{
		return '';
	}

	public function fnDump($var)
	{
		dump($var);
	}

	public function fnTitle($title, $clean = false): string
	{
		if (!$clean)
		{
			$title = $title . ' | ' . $this->fnGetOption('boardTitle');
		}

		$title = trim("<title>$title</title>");

		return new \Twig\Markup($title, 'UTF-8');
	}

	public function fnGetOption($id): string
	{
		return '';
	}

	public function fnCssSrc($templateName, $type = 'pub')
	{
		$containerLess = XM::app()->container()->offsetGet('local.lessFiles');

		XM::app()->container()->offsetSet('local.lessFiles', array_merge($containerLess, [
			$templateName => strtolower($type)
		]));
	}

	public function fnLink($link, $params = [])
	{
		return XM::app()->templater()->buildLink($link, $params);
	}

	public function filterGetEntity($entity, $property)
	{
		return $entity->$property;
	}
}