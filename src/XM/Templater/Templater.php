<?php

namespace XM\Templater;

use Latte\Engine;
use XM\Mvc\View;

class Templater
{
	public array $functions = [
		'css'  => 'fnCss',
		'dump' => 'fnDump',
	];

	protected string $templateName = '';
	protected ?Engine $templater = null;

	public function getTemplater(): ?Engine
	{
		if ($this->templater === null)
		{
			$this->templater = new Engine();

			// Set functions
			foreach ($this->functions as $functionName => $function)
			{
				$extension = new TemplaterExtension();

				$this->templater->addFunction($functionName, function (...$args) use ($extension, $functionName, $function)
				{
					return $extension->$function(...$args);
				});
			}

			// Add "globals" func
			$this->templater->addFunction('xm_globals', function ($key = null)
			{
				$app = \XM::app();
				$request = $app->request();

				$data = [
					'versionId' => \XM::$version,
					'app'       => $app,
					'request'   => $request,
					'uri'       => $request->getRequestUri(),
					'fullUri'   => $request->getFullRequestUri(),
					'time'      => \XM::$time,
					'visitor'   => $app->visitor(),
					'session'   => $app->session(),
					'template'  => $this->templateName ?: null
				];

				if ($key)
				{
					return $data[$key] ?? null;
				}

				return $data;
			});

			// Add "container" func
			$this->templater->addFunction('xm_container', function ($key = null)
			{
				return \XM::app()->container($key);
			});
		}

		return $this->templater;
	}

	public function renderTemplate($templateName, array $viewParams = []): View
	{
		$this->templateName = $templateName;

		return new View(
			$this->getTemplater(),
			$this->getTemplateFullPath($templateName),
			$viewParams
		);
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

	public function getTemplateFullPath($templateName, $type = 'Pub'): string
	{
		return \XM::getRootDirectory() . '\internal_data\templates\\' . $type . '\\' . $templateName . '.latte';
	}

	public function getIncludedLess(): string
	{
		$lessFiles = \XM::app()->container('local.lessFiles');
		$params = [];

		foreach ($lessFiles as $template => $type)
		{
			$params[] = 'template[]=' . urlencode($template);
			$params[] = 'type[]=' . urlencode($type);
		}

		return \XM::app()->request()->getFullRequestUri() . 'css.php?' . implode('&', $params);
	}
}
