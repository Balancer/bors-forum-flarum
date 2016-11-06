<?php

namespace B2\Flarum;

class TopicTags extends ObjectDb
{
	function table_name() { return \B2\Cfg::get('flarum.db.prefix').'discussions_tags'; }

	function table_fields()
	{
		return [
			'id' => 'discussion_id,tag_id',
			'topic_id' => 'discussion_id',
			'tag_id',
		];
	}
}
