<?php

namespace B2\Flarum;

class Post extends ObjectDb
{
	function table_name() { return \B2\Cfg::get('flarum.db.prefix').'posts'; }

	function table_fields()
	{
		return [
			'id',
			'topic_id' => [ 'name' => 'discussion_id', 'class' => Topic::class ],
			'number',
			'create_time' => 'UNIX_TIMESTAMP(`time`)',
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

	function body_no_title()
	{
		$html = $this->body();

		$title = trim($this->topic()->title());

		$html = preg_replace('!<h2>\s*'.preg_quote($title, '!').'\s*</h2>!is', '', $html);

		return $html;
	}

	function body_no_title_origin()
	{
		$html = $this->body_no_title();

		$html = preg_replace('!<h2>\s*'.preg_quote($title, '!').'\s*</h2>!is', '', $html);
		$html = preg_replace("!<hr>\n<p><a href=\".+?\" target=\"_blank\" rel=\"nofollow noreferrer\">Источник</a></p>!", '', $html);
		$html = preg_replace("!<p><a href=\".+?\" target=\"_blank\" rel=\"nofollow noreferrer\">Источник</a></p>!", '', $html);

		return $html;
	}
}
