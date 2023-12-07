<?php

namespace Marshmallow\NovaActivity\Traits;

use Marshmallow\NovaActivity\Models\NovaActivity;

trait NovaActivities
{
    public function novaActivity()
    {
        return $this->morphMany(NovaActivity::class, 'nova_activity')
            ->orderBy('is_pinned')
            ->orderBy('created_at');
    }
}
