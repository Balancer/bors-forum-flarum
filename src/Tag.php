<?php

namespace B2\Flarum;

class Tag extends ObjectDb
{
	function table_name() { return \B2\Cfg::get('flarum.db.prefix').'tags'; }

	function table_fields()
	{
		return [
			'id',
			'name',
			'slug',
			'description',
			'color',
			'background_path',
			'background_mode',
			'position',
			'parent_id',
			'default_sort',
			'is_restricted',
			'is_hidden',
			'discussions_count',
			'last_time',
			'last_discussion_id',
		];
	}
}
