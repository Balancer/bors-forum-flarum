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
			'content' => ['type' => 'markdown'],
			'modify_time' => 'edit_time',
			'last_editor_id' => 'edit_user_id',
			'hide_time',
			'hide_user_id',
			'ip_address',
			'is_approved',
		];
	}
}
