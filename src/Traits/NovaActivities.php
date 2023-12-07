<?php

namespace Marshmallow\NovaActivity\Traits;

use Carbon\Carbon;
use Marshmallow\NovaActivity\Models\NovaActivity;

trait NovaActivities
{
    public function addActivity(
        int $user_id = null,
        int|string $type = null,
        int|string $label = null,
        string $comment = null,
        Carbon $created_at = null,
        array $quick_replies = [],
    ) {
        $this->novaActivity()->create([
            'user_id' => $user_id,
            'type_key' => $type,
            'type_label' => $label,
            'comment' => $comment,
            'created_at' => $created_at,
            'meta' => [
                'quick_replies' => $quick_replies,
            ],
        ]);
    }

    public function novaActivity()
    {
        return $this->morphMany(NovaActivity::class, 'nova_activity')
            ->orderBy('is_pinned')
            ->orderBy('created_at');
    }
}
