<?php

namespace B2\Flarum;

class Post extends ObjectDb
{
	function table_name() { return \B2\Cfg::get('flarum.db.prefix').'posts'; }

	function table_fields()
	{
		return [
			'id',
			'topic_id' => 'discussion_id',
			'number',
//			'create_time' => ['name' => 'UNIX_TIMESTAMP(time)'],
			'create_datetime' => 'time',
			'owner_id' => 'user_id',
			'type',
			'text' => ['name' => 'content', 'type' => 'markdown'],
			'modify_time' => 'edit_time',
			'last_editor_id' => 'edit_user_id',
			'hide_time',
			'hide_user_id',
			'ip_address',
			'is_approved',
		];
	}

	function body()
	{
		$app = App::instance();
		$flarum_app = $app->flarum_app;

        $flarum_app->register('Flarum\Formatter\FormatterServiceProvider');

		$formatter = $flarum_app->make('flarum.formatter');

		$html = $formatter->render($this->text());
		return $html;
	}
}
