<?php

namespace XM\Controller\Pub;

use XM\Mvc\Controller;
use XM\Mvc\View;

class Index extends Controller
{
	public function actionIndex(): View
	{
		return $this->view('index', [
			'id' => '1234'
		]);
	}
}