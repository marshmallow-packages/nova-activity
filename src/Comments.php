<?php

namespace Marshmallow\Comments;

use Laravel\Nova\Fields\Field;

class Comments extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'comments';

    public function __construct(...$params)
    {
        parent::__construct(...$params);
        $this->quickReplies()
            ->addCurrentUser()
            ->activityTitle(__('Comments'))
            ->fillUsing(function () {
                //
            });
    }

    public function quickReplies(): self
    {
        return $this->withMeta([
            'quick_replies' => array_merge(config('nova-commentable.quick_replies'), [
                '' => [
                    'name' => __('I feel nothing'),
                    'color' => '#ccc',
                    'background' => '#fff',
                    'icon' => 'x',
                    'solid_icon' => false,
                ]
            ]),
        ]);
    }

    public function types(callable|array $types): self
    {
        $types = is_array($types) ? $types : $types();
        return $this->withMeta([
            'types' => $types,
        ]);
    }

    public function addCurrentUser(): self
    {
        $user = auth()->user();

        return $this->withMeta([
            'user' => [
                'id' => $user->id,
                'avatar' => self::getUserAvatar($user),
            ]
        ]);
    }

    public function activityTitle(string $title): self
    {
        return $this->withMeta([
            'activity_title' => $title,
        ]);
    }

    public static function getUserAvatar($user): string
    {
        $avatar = md5($user->email);
        return method_exists($user, 'avatarPath')
            ? $user->avatarPath()
            : "https://www.gravatar.com/avatar/{$avatar}?s=300";
    }
}
