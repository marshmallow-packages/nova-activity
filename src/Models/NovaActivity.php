<?php

namespace Marshmallow\NovaActivity\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Marshmallow\NovaActivity\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Marshmallow\NovaActivity\Events\ActivityPinned;
use Marshmallow\NovaActivity\Events\ActivityDeleted;
use Marshmallow\NovaActivity\Events\ActivityUnpinned;
use Marshmallow\NovaActivity\Events\ActivityCommentShow;
use Marshmallow\NovaActivity\Events\ActivityCommentHidden;

class NovaActivity extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'array',
        'mentions' => 'array',
    ];

    protected $table = 'nova_activity';

    public function runAction(string $action)
    {
        $action = match ($action) {
            'pin' => function () {
                $this->update(['is_pinned' => true]);
                event(new ActivityPinned($this, auth()?->user()));
            },
            'unpin' => function () {
                $this->update(['is_pinned' => false]);
                event(new ActivityUnpinned($this, auth()?->user()));
            },
            'hide' => function () {
                $this->update(['is_hidden' => true]);
                event(new ActivityCommentHidden($this, auth()?->user()));
            },
            'show' => function () {
                $this->update(['is_hidden' => false]);
                event(new ActivityCommentShow($this, auth()?->user()));
            },
            'delete' => function () {
                $this->delete();
                event(new ActivityDeleted($this, auth()?->user()));
            },
        };

        return $action();
    }

    public function getOtherQuickReplies()
    {
        $user = auth()->user();
        return collect(Arr::get($this->meta, 'quick_replies', []))->reject(function ($icon, $quick_reply_user) use ($user) {
            return $quick_reply_user == "user_{$user?->id}";
        })->toArray();
    }

    public function hasMentions(): bool
    {
        return is_array($this->mentions) && !empty($this->mentions);
    }

    public function getMentions(): ?Collection
    {
        if (!$this->hasMentions()) {
            return null;
        }

        $collection = collect();
        collect($this->mentions)->each(function ($mention) use (&$collection) {
            $class = Arr::get($mention, 'class');
            $model = $class::find(Arr::get($mention, 'key'));
            $collection->push($model);
        });
        return $collection;
    }

    public function user()
    {
        if (in_array(SoftDeletes::class, class_uses_recursive(Activity::$userModel))) {
            return $this->belongsTo(Activity::$userModel)->withTrashed();
        }
        return $this->belongsTo(Activity::$userModel);
    }

    public function novaActivity()
    {
        return $this->morphTo();
    }
}
