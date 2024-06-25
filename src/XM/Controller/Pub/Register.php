<?php

namespace XM\Controller\Pub;

use XM\Mvc\Controller;
use XM\Mvc\View;
use XM\Service\Auth;

class Register extends Controller
{
	public function actionIndex(): ?View
	{
		if (\XM::visitor())
		{
			return $this->redirect('/');
		}

		if ($this->isPost())
		{
			$input = [
				'login'    => $this->filter('login'),
				'email'    => $this->filter('email'),
				'password' => $this->filter('password')
			];

			/** @var Auth $user */
			$user = $this->getAuthService()->registerUser($input);
			if ($user)
			{
				return $this->redirect('/');
			}
		}

		return $this->view('register');
	}

	protected function getAuthService(): \XM\Service\AbstractService
	{
		return \XM::app()->service('XM:Auth');
	}
}