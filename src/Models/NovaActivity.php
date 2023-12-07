<?php

namespace Marshmallow\NovaActivity\Models;

use Illuminate\Support\Arr;
use Marshmallow\NovaActivity\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NovaActivity extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'array',
    ];

    protected $table = 'nova_activity';

    public function runAction(string $action)
    {
        return match ($action) {
            'pin' => $this->update(['is_pinned' => true]),
            'unpin' => $this->update(['is_pinned' => false]),
            'hide' => $this->update(['is_hidden' => true]),
            'show' => $this->update(['is_hidden' => false]),
            'delete' => $this->delete(),
        };
    }

    public function getOtherQuickReplies()
    {
        $user = auth()->user();
        return collect(Arr::get($this->meta, 'quick_replies', []))->reject(function ($icon, $quick_reply_user) use ($user) {
            return $quick_reply_user == "user_{$user?->id}";
        })->toArray();
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
