<?php

namespace Marshmallow\NovaActivity\Traits;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Marshmallow\NovaActivity\Activity;
use Marshmallow\NovaActivity\Models\NovaActivity;

trait NovaActivities
{
    public function addActivity(
        int $user_id = null,
        int|string $type = null,
        int|string|array $label = null,
        string $comment = null,
        Carbon $created_at = null,
        array $quick_replies = [],
        array $mentions = [],
    ): NovaActivity {
        $label = is_array($label) ? Arr::get($label, 'text') : $label;

        return $this->novaActivity()->create([
            'user_id' => $user_id,
            'type_key' => $type,
            'type_label' => $label,
            'comment' => $comment,
            'created_at' => $created_at,
            'meta' => [
                'quick_replies' => $quick_replies,
            ],
            'mentions' => $mentions,
        ]);
    }

    public function novaActivity()
    {
        return $this->morphMany(Activity::$activityModel, 'nova_activity')
            ->orderBy('is_pinned')
            ->orderBy('created_at');
    }
}
