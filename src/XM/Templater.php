<?php

namespace XM;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use XM\Mvc\View;
use XM\Twig\TemplaterExtension;

class Templater
{
	protected string $templates = '\internal_data\templates\\';
	protected string $templateName = '';
	protected ?Environment $twig = null;

	public function getTemplater(): ?Environment
	{
		$app = \XM::app();
		$request = $app->request();

		if ($this->twig === null)
		{
			$loader = new FilesystemLoader(\XM::getRootDirectory() . $this->templates . 'Pub');
			$this->twig = new Environment($loader, ['autoescape' => false]);
			$this->twig->addExtension(new TemplaterExtension);
		}

		$data = [
			'versionId' => \XM::$version,
			'app'       => $app,
			'request'   => $request,
			'uri'       => $request->getRequestUri(),
			'fullUri'   => $request->getFullRequestUri(),
			'time'      => \XM::$time,
			'visitor'   => \XM::app()->visitor(),
			'session'   => \XM::app()->session(),
			'template'  => $this->templateName ?: null
		];

		$this->twig->addGlobal('XM', $data);

		return $this->twig;
	}

	public function buildLink($link, $params = []): string
	{
		$explode = explode('|', $link);

		if (count($explode) > 1)
		{
			if ($params)
			{
				return $explode[1] .= '?' . http_build_query($params);
			}
			else
			{
				return $explode[1];
			}
		}

		if ($params)
		{
			$link .= '?' . http_build_query($params);
		}

		return trim(\XM::app()->request()->getHostUrl() . '/' . $link, '/');
	}

	public function getLocalIncludedLessFiles()
	{
		return \XM::app()->container()->offsetGet('local.lessFiles');
	}

	public function renderTemplate($templateName, array $viewParams = []): View
	{
		$this->templateName = $templateName;

		$template = $templateName . '.html';

		return new View($this->getTemplater(), $template, $viewParams);
	}
}
