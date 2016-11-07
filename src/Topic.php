<?php

namespace B2\Flarum;

class Topic extends ObjectDb
{
	function table_name() { return \B2\Cfg::get('flarum.db.prefix').'discussions'; }

	function table_fields()
	{
		return [
			'id',
			'title',
			'comments_count',
			'participants_count',
			'number_index',
			'create_time' => 'UNIX_TIMESTAMP(`start_time`)',
			'author_id' => ['name' => 'start_user_id', 'class' => User::class],
			'start_post_id' => ['class' => Post::class],
			'last_time',
			'last_user_id',
			'last_post_id',
			'last_post_number',
			'hide_time',
			'hide_user_id',
			'slug',
			'is_approved',
			'is_locked',
			'is_sticky',
		];
	}

	function recalculate()
	{
		$app = \Infonesy\Driver\Flarum\App::instance();
		$flarum_app = $app->flarum_app;
		$flarum_topic = \Flarum\Core\Discussion::find($this->id());
		$flarum_topic->refreshCommentsCount();
        $flarum_topic->refreshLastPost();
        $flarum_topic->refreshParticipantsCount();
        $flarum_topic->save();
	}

	function url() { return 'http://forum.bionco.ru/d/'.$this->id().'-'.$this->slug(); }
}
