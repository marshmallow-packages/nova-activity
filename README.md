```bash
composer require marshmallow/nova-activity
```

```bash
php artisan vendor:publish --tag="nova-activity-config"
```

```bash
php artisan vendor:publish --tag="nova-activity-migrations"
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