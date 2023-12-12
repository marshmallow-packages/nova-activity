<?php

namespace Marshmallow\NovaActivity;

use Laravel\Nova\Fields\Field;

class Activity extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'activity';

    public static $userModel = \App\Models\User::class;

    public function __construct(...$params)
    {
        parent::__construct(...$params);
        $this->quickReplies()
            ->addCurrentUser()
            ->useComments(config('nova-activity.use_comments'))
            ->setLocale(config('app.locale'))
            ->dateFormat('Do MMM, YYYY')
            ->activityTitle(__('novaActivity.title'))
            ->fillUsing(function () {
                //
            });
    }

    public function quickReplies(): self
    {
        return $this->withMeta([
            'quick_replies' => array_merge(config('nova-activity.quick_replies'), [
                '' => [
                    'name' => __('novaActivity.i_feel_nothing'),
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

    public function limit(int $limit)
    {
        return $this->withMeta([
            'limit' => $limit,
        ]);
    }

    public function mentions(array|callable $mentions)
    {
        $mentions = is_callable($mentions) ? $mentions() : $mentions;
        return $this->withMeta([
            'mentions' => collect($mentions)->map(function ($mention) {
                if (array_key_exists('model', $mention)) {
                    $mention['model'] = [
                        'class' => get_class($mention['model']),
                        'key' => $mention['model']->getKey(),
                    ];
                }
                return $mention;
            })->toArray(),
        ]);
    }

    public function useComments(bool $use_comments)
    {
        return $this->withMeta([
            'use_comments' => $use_comments,
        ]);
    }

    public function setLocale(string|callable $locale)
    {
        $locale = is_callable($locale) ? $locale() : $locale;
        return $this->withMeta([
            'locale' => $locale,
        ]);
    }

    public function dateFormat(string $date_format)
    {
        return $this->withMeta([
            'date_format' => $date_format,
        ]);
    }

    public function alwaysShowComments()
    {
        return $this->withMeta([
            'always_show_comments' => true,
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

    public function activityTitle(string|null $title): self
    {
        return $this->withMeta([
            'activity_title' => $title,
        ]);
    }

    public static function getUserAvatar($user = null): string
    {
        if (!$user) {
            return '';
        }
        $avatar = md5($user->email);
        return method_exists($user, 'avatarPath')
            ? $user->avatarPath()
            : "https://www.gravatar.com/avatar/{$avatar}?s=300";
    }
}
