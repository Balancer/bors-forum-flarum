<?php

namespace B2\Flarum;

require \B2\Cfg::get('flarum.root').'/vendor/autoload.php';

class FlarumServerAdapter extends \Flarum\Forum\Server
{
	public function getFlarumApp()
	{
		$app = $this->getApp();
		return $app;
	}
}

class App
{
	var $flarum_server;
	var $flarum_app;

	static function instance()
	{
		static $instance;
		if(empty($instance))
		{
			$app = new App;
			$app->flarum_server = new FlarumServerAdapter(\B2\Cfg::get('flarum.root'));
			$app->flarum_app = $app->flarum_server->getFlarumApp();
			$instance = $app;
		}

		return $instance;
	}
}
