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
    quick_replies: $quick_replies,
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
