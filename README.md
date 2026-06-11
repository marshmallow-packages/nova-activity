![alt text](https://marshmallow.dev/cdn/media/logo-red-237x46.png "marshmallow.")

# Nova Activity

[![Latest Version on Packagist](https://img.shields.io/packagist/v/marshmallow/nova-activity.svg?style=flat-square)](https://packagist.org/packages/marshmallow/nova-activity)
[![Total Downloads](https://img.shields.io/packagist/dt/marshmallow/nova-activity.svg?style=flat-square)](https://packagist.org/packages/marshmallow/nova-activity)

A Laravel Nova field that adds a timeline of activities, comments and quick replies to a resource. Activities are stored as a polymorphic relation on your own models, support mentions, file uploads, custom actions and emit events for every interaction.

## Installation

Install the package via Composer:

```bash
composer require marshmallow/nova-activity
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="nova-activity-migrations"
php artisan migrate
```

Publish the config file:

```bash
php artisan vendor:publish --tag="nova-activity-config"
```

Optionally publish the translations:

```bash
php artisan vendor:publish --tag="nova-activiy-translations"
```

## Setup

Add the `NovaActivities` trait to any model that should record activities:

```php
use Marshmallow\NovaActivity\Traits\NovaActivities;

class Order extends Model
{
    use NovaActivities;
}
```

Add the `Activity` field to the corresponding Nova resource:

```php
use Marshmallow\NovaActivity\Activity;

public function fields(NovaRequest $request)
{
    return [
        Activity::make(__('Activity'))
            ->types(function () {
                return [
                    1 => 'Called the customer and spoke to them.',
                    2 => 'Customer will send additional information.',
                ];
            })
            ->showOnPreview(),
    ];
}
```

When the `Activity` field is shown on the index view, add the following to the
resource so the row click action does not interfere with the field:

```php
public static $clickAction = 'ignore';
```

## Configuration

The published `config/nova-activity.php` file exposes the following options:

| Key | Default | Description |
| --- | --- | --- |
| `table_name` | `nova_activity` | Table used to store activities. |
| `use_quick_replies` | `true` | Enable the quick-reply (reaction) buttons. |
| `use_comments` | `true` | Allow writing comments on activities. |
| `use_file_uploads` | `false` | Allow attaching file uploads to activities. |
| `dates.format` | `d M, Y \a\t H:i` | PHP date format. |
| `dates.js_format` | `Do MMM, YYYY` | moment.js date format used in the field UI. |
| `dates.use_human_readable` | `true` | Render dates as human-readable, relative strings. |
| `comment_validation.type` | `required` | Validation rule applied to the comment input. |
| `quick_replies` | array of reactions | The available quick replies (name, background, icon, solid_icon). |

Each quick reply is defined as:

```php
'quick_replies' => [
    'exited' => [
        'name' => 'Exited',
        'background' => '#ef4444',
        'icon' => 'fire',
        'solid_icon' => false,
    ],
    // ...
],
```

## Usage

### Adding activities from code

The `NovaActivities` trait adds an `addActivity()` method to your model:

```php
use Carbon\Carbon;

$model->addActivity(
    user_id: $request->user()->id,
    type: $request->type,
    label: $request->type_label,
    comment: $request->comment,
    created_at: Carbon::parse($request->date),
    quick_replies: [
        'user_1' => 'sad',
    ],
    mentions: [
        [
            'key' => 1,
            'class' => User::class,
        ],
    ],
);
```

All arguments are optional; only the ones you pass are stored.

### Field options

The `Activity` field exposes a fluent API. All of the methods below return the
field instance so they can be chained:

```php
Activity::make(__('Activity'))
    ->activityTitle('Activity title')   // pass null to hide the title
    ->useComments(true)
    ->useQuickReplies(true)
    ->useFileUploads(true)
    ->useHumanReadableDates(true)
    ->alwaysShowComments()
    ->truncateComments(100)
    ->jsDateFormat('Do MMM, YYYY')       // moment.js format
    ->setLocale('nl');
```

`setLocale()` also accepts a closure:

```php
->setLocale(function () {
    return auth()->user()->locale;
})
```

### Limiting the number of activities shown

```php
->limit(3)            // limit on index, detail and forms
->limitOnDetail(null) // null shows all
->limitOnIndex(3)
->limitOnForms(10)
```

### Types

`types()` accepts an array or a closure mapping a value to a label:

```php
->types(function () {
    return [
        2 => 'Called the customer and spoke to them.',
    ];
})
```

### Mentions

Provide the list of mentionable users with `mentions()`:

```php
->mentions(function (): array {
    return User::get()->map(function ($user) {
        return [
            'model' => $user,
            'value' => str_slug($user->name),
            'avatar_url' => Activity::getUserAvatar($user),
            'key' => $user->name,
        ];
    })->toArray();
})
```

On a stored activity you can inspect the mentions:

```php
/** Check if this activity has any mentions. */
$activity->hasMentions();

/** Get a collection of all the classes that were mentioned. */
$activity->getMentions();
```

### Avatars

`Activity::getUserAvatar($user)` falls back to a Gravatar based on the user's
email. To use a custom avatar, add an `avatarPath()` method to your user model:

```php
// App\Models\User
public function avatarPath()
{
    return 'https://example.com/avatars/me.jpg';
}
```

### Custom actions

Attach actions to the field with `actions()`:

```php
use Marshmallow\NovaActivity\Actions\ToggleTagAction;

->actions([
    ToggleTagAction::make(__('Important'), 'important')
        ->color('red')
        ->backgroundColor('#ffffff')
        ->borderColor('red')
        ->icon('fire'),
])
```

## Events

Every interaction dispatches an event. Listen for them in your `EventServiceProvider`:

```php
use App\Listeners\DoWhatEver;
use Marshmallow\NovaActivity\Events\ActivityCreated;

protected $listen = [
    ActivityCreated::class => [
        DoWhatEver::class,
    ],
];
```

The available events are:

| Event | Public properties |
| --- | --- |
| `ActivityCreated` | `$activity` |
| `ActivityDeleted` | `$activity`, `$user` |
| `ActivityPinned` | `$activity`, `$user` |
| `ActivityUnpinned` | `$activity`, `$user` |
| `ActivityCommentHidden` | `$activity`, `$user` |
| `ActivityCommentShow` | `$activity`, `$user` |
| `QuickReplyChanged` | `$activity`, `$user` |

Inside a listener you can reach the activity and the model it belongs to:

```php
class DoWhatEver
{
    public function handle($event)
    {
        /** The model on which the activity was created. */
        $model = $event->activity->nova_activity;

        /** The activity itself. */
        $activity = $event->activity;

        /** Mentions. */
        $activity->hasMentions();
        $activity->getMentions();
    }
}
```

## Credits

- [Marshmallow](https://github.com/marshmallow-packages)
- [All Contributors](https://github.com/marshmallow-packages/nova-activity/contributors)

## Security Vulnerabilities

Please report security vulnerabilities by email rather than via the public issue tracker.

## License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.
