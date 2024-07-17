<?php

namespace Marshmallow\NovaActivity;

use Laravel\Nova\Fields\Field;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Http\Requests\UpdateResourceRequest;
use Marshmallow\NovaActivity\Http\Controllers\CreateActivityController;

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
            ->jsDateFormat(config('nova-activity.dates.js_format'))
            ->useHumanReadableDates(config('nova-activity.dates.use_human_readable'))
            ->useFileUploads(config('nova-activity.use_file_uploads'))
            ->useQuickReplies(config('nova-activity.use_quick_replies'))
            ->activityTitle(__('novaActivity.title'))
            ->fillUsing(function (UpdateResourceRequest $request, Model $resource, $field_name) {
                $activity_data = json_decode($request->get($field_name), true);
                /**
                 * We use a prefixer so we don't override attributes from
                 * other fields in the nova resource. This was introduced
                 * because we were overriding the "comments" field.
                 */
                $key_prefixer = 'nova_activity_entry_data_';
                collect($activity_data)->each(function ($value, $key) use (&$request, $key_prefixer) {
                    $request->merge([
                        $key_prefixer . $key => $value,
                    ]);
                });

                (new CreateActivityController)
                    ->storeNewActivity($resource, $request, $key_prefixer);
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
        $types = collect($types)->map(function ($text, $value) {
            return [
                'text' => $text,
                'value' => $value,
            ];
        })->values()->toArray();

        return $this->withMeta([
            'types' => $types,
        ]);
    }

    public function limit(int|null $limit)
    {
        return $this->limitOnDetail($limit)
            ->limitOnIndex($limit)
            ->limitOnForms($limit);
    }

    public function limitOnDetail(int|null $limit)
    {
        return $this->withMeta([
            'limit_on_detail' => $limit,
        ]);
    }

    public function limitOnIndex(int|null $limit)
    {
        return $this->withMeta([
            'limit_on_index' => $limit,
        ]);
    }

    public function limitOnForms(int|null $limit)
    {
        return $this->withMeta([
            'limit_on_forms' => $limit,
        ]);
    }

    public function useHumanReadableDates(bool $use_human_readable_dates)
    {
        return $this->withMeta([
            'use_human_readable_dates' => $use_human_readable_dates,
        ]);
    }

    public function useFileUploads(bool $use_file_uploads)
    {
        return $this->withMeta([
            'use_file_uploads' => $use_file_uploads,
        ]);
    }

    public function useQuickReplies(bool $use_quick_replies)
    {
        return $this->withMeta([
            'use_quick_replies' => $use_quick_replies,
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

    public function jsDateFormat(string $js_date_format)
    {
        return $this->withMeta([
            'js_date_format' => $js_date_format,
        ]);
    }

    public function alwaysShowComments(bool $show_comments = true)
    {
        return $this->withMeta([
            'always_show_comments' => $show_comments,
        ]);
    }

    public function truncateComments(int $char_count = 100)
    {
        return $this->withMeta([
            'truncate_comments' => $char_count,
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
