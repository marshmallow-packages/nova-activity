<?php

namespace Marshmallow\Comments\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

class NovaComment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'array',
    ];

    protected $table = 'nova_commentable';

    public function getOtherQuickReplies($user)
    {
        return collect(Arr::get($this->meta, 'quick_replies', []))->reject(function ($icon, $quick_reply_user) use ($user) {
            return $quick_reply_user == "user_{$user->id}";
        })->toArray();
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function novaComments()
    {
        return $this->morphTo();
    }
}
