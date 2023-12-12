<?php

namespace Marshmallow\NovaActivity\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Marshmallow\NovaActivity\Models\NovaActivity;

class ActivityCommentHidden
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public NovaActivity $activity, public ?Model $user)
    {
        //
    }
}
