```bash
composer require marshmallow/nova-activity
```

```bash
php artisan vendor:publish --tag="nova-activity-config"
```

```bash
php artisan vendor:publish --tag="nova-activity-migrations"
```

```bash
php artisan vendor:publish --tag="nova-activiy-translations"
```

```php
// Model
use Marshmallow\NovaActivity\Traits\NovaActivities;
use NovaActivities;
```

```php
// Resource
use Marshmallow\NovaActivity\Activity;
Activity::make(__('Comments Field Name'))
    ->types(function () {
        return [
            2 => 'Klant gebeld en gesproken. Mailt aanvullende info',
            // ...
        ];
    })
    ->activityTitle(null)
    ->showOnPreview(),
```

```php
->activityTitle('Activity toch!')
->activityTitle(null)
```

```php
$model->addActivity(
    user_id: $request->user()->id,
    type: $request->type,
    label: $request->type_label,
    comment: $request->comment,
    created_at: Carbon::parse($request->date)->setTimeFromTimeString(
        now()->format('H:i:s')
    ),
    quick_replies: [
        'user_1' => 'sad',
    ],
    mentions: [
        [
            'key' => 1,
            'class' => User::class,
        ]
    ]
);
```

```php
// App\Models\User
public function avatarPath()
{
    return 'https://images.unsplash.com/photo-1550525811-e5869dd03032?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80';
}
```

```php
 'quick_replies' => [
    'exited' => [
        'name' => 'Exited',
        'background' => '#ef4444',
        'icon' => 'fire',
        'solid_icon' => false,
    ],
```

```php
->types(function () {
    return [
        2 => 'Klant gebel
```

```php
->limit(3)
->alwaysShowComments()
```

```php
// momentjs format
->dateFormat('DD MMM YYYY')
```

```php
// momentjs format
->setLocale('nl')
->setLocale(function () {
    return auth()->user()->locale;
})
```

```php
->mentions(
    function (): array {
        return User::get()->map(function ($user) {
            return [
                'model' => $user,
                'value' => str_slug($user->name),
                'avatar_url' => Activity::getUserAvatar($user),
                'key' => $user->name,
            ];
        })->toArray();
    },
)
```

```php
return [
    'use_comments' => false,
]

useComments(false)
```

```php
use App\Listeners\DoWhatEver;
use Marshmallow\NovaActivity\Events\ActivityCreated;
protected $listen = [
    ActivityCreated::class => [
        DoWhatEver::class
    ],
];
```
