<?php

namespace B2\Flarum;

class User extends ObjectDb
{
	function table_name() { return \B2\Cfg::get('flarum.db.prefix').'users'; }

	function table_fields()
	{
		return [
			'id',
			'title' => 'username',
			'email',
			'is_activated',
			'password',
			'bio',
			'avatar_path',
			'preferences',
			'create_time' => [ 'name' => 'UNIX_TIMESTAMP(join_time)' ],
			'last_visit_time' => [ 'name' => 'UNIX_TIMESTAMP(last_seen_time)' ],
			'read_time',
			'notifications_read_time',
			'discussions_count',
			'comments_count',
			'flags_read_time',
			'suspend_until',
			'twitter_id',
		];
	}
}
