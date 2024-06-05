<?php

namespace XM\Mvc;

use lessc;

class Less
{
	public string $template;

	public function __construct($templates)
	{
		$this->templates = $templates;
	}

	public function compiler(): lessc
	{
		require 'src/vendor/leafo/lessphp/lessc.inc.php';

		return new lessc;
	}

	public function compile(): string
	{
		$compiler = $this->compiler();

		try
		{
			$output = '';

			foreach ($this->templates as $template => $type)
			{
				$dir = 'internal_data/templates/' . ($type == 'pub' ? 'Pub' : 'Admin') . '/';

				if (file_exists($dir . $template))
				{
					$compiledLess = $compiler->compileFile($dir . $template);

					$output .= "/********* {$type}:{$template} ********/" . PHP_EOL . $this->minify($compiledLess) . PHP_EOL . PHP_EOL;
				}
			}

			return $output;
		}
		catch (\Exception $e)
		{
			return "<div class=\"lessCompilerError\">$e->getMessage();</div>";
		}
	}

	public function minify($css)
	{
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
		$css = preg_replace('/\s*([{}|:;,])\s+/', '$1', $css);

		return str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $css);
	}
}