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
			'author_id' => 'user_id',
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

//		$html = preg_replace("!(<a href=\".+?\" target=\"_blank\" rel=\"nofollow noreferrer\")(>Источник</a>)!", '$1 class="btn btn-default"$2', $html);

		return $html;
	}

	function origin()
	{
		$html = $this->body();
		if(preg_match("!<a href=\"(.+?)\" target=\"_blank\" rel=\"nofollow noreferrer\">Источник</a>!", $html, $m))
			return $m[1];

		return NULL;
	}

	function body_no_title_origin()
	{
		$html = $this->body_no_title();

		$html = preg_replace('!<h2>\s*'.preg_quote($title, '!').'\s*</h2>!is', '', $html);
		$html = preg_replace("!<hr>\n<p><a href=\".+?\" target=\"_blank\" rel=\"nofollow noreferrer\">Источник</a></p>!", '', $html);
		$html = preg_replace("!<p><a href=\".+?\" target=\"_blank\" rel=\"nofollow noreferrer\">Источник</a></p>!", '', $html);

		return $html;
	}

	static function create($data)
	{
		$app = App::instance();
		$flarum_app = $app->flarum_app;
//		$flarum_actor = Sub\User::find(popval($data, 'author_id'));
		$flarum_actor = \Flarum\Core\User::find(popval($data, 'author_id'));
		$flarum_discussion = \Flarum\Core\Discussion::find(popval($data, 'topic_id'));

//		dump($data, $flarum_discussion);

//		$flarum_actor->forced_time = true;

		$flarum_data = [
			'attributes' => [
				'content' => $data['text'],
//				'time' => \Carbon\Carbon::createFromTimestampUTC($data['create_time'])->toDateTimeString(),
				'is_approved' => true,
			],
		];

		$ipAddress = popval($data, 'author_ip');

		$cmd = new \Flarum\Core\Command\PostReply($flarum_discussion->id, $flarum_actor, $flarum_data, $ipAddress);
		$handler = new \Flarum\Core\Command\PostReplyHandler(
			$flarum_app->events,
			new	\Flarum\Core\Repository\DiscussionRepository,
			new	\Flarum\Core\Notification\NotificationSyncer(new \Flarum\Core\Repository\NotificationRepository, new \Flarum\Core\Notification\NotificationMailer($flarum_app->mailer)),
			new \Flarum\Core\Validator\PostValidator($flarum_app->validator, $flarum_app->events, $flarum_app->make(\Symfony\Component\Translation\TranslatorInterface::class))
		);

		$flarum_original_post = $handler->handle($cmd);

		$flarum_b2_post = Post::load($flarum_original_post->id);
		return $flarum_b2_post;
	}

	function edit($data)
	{
		$app = App::instance();
		$flarum_app = $app->flarum_app;
		$flarum_actor = \Flarum\Core\User::find($this->author_id());
		$flarum_discussion = \Flarum\Core\Discussion::find(popval($data, 'topic_id'));

//		dump($data, $flarum_discussion);

//		$flarum_actor->forced_time = true;

		$flarum_data = [
			'attributes' => [
				'content' => $data['text'],
//				'time' => \Carbon\Carbon::createFromTimestampUTC($data['create_time'])->toDateTimeString(),
				'is_approved' => true,
			],
		];

		$ipAddress = popval($data, 'author_ip');

    /**
     * @param int $postId The ID of the post to edit.
     * @param User $actor The user performing the action.
     * @param array $data The attributes to update on the post.
     */
//    public function __construct($postId, User $actor, array $data)

		$cmd = new \Flarum\Core\Command\EditPost($this->id(), $flarum_actor, $flarum_data);

    /**
     * @param Dispatcher $events
     * @param PostRepository $posts
     * @param PostValidator $validator
     */
//    public function __construct(Dispatcher $events, PostRepository $posts, PostValidator $validator)

		$handler = new \Flarum\Core\Command\EditPostHandler(
			$flarum_app->events,
			new	\Flarum\Core\Repository\PostRepository,
			new \Flarum\Core\Validator\PostValidator($flarum_app->validator, $flarum_app->events, $flarum_app->make(\Symfony\Component\Translation\TranslatorInterface::class))
		);

		$flarum_original_post = $handler->handle($cmd);

		$flarum_b2_post = Post::load($flarum_original_post->id);
		return $flarum_b2_post;
	}
}
