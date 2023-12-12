<?php

namespace Marshmallow\NovaActivity\Http\Controllers;

use Illuminate\Http\Request;
use Marshmallow\NovaActivity\Models\NovaActivity;
use Marshmallow\NovaActivity\Events\QuickReplyChanged;

class SetQuickReplyController
{
    public function __invoke($activity_id, Request $request)
    {
        $activity = NovaActivity::find($activity_id);
        $meta = $activity->meta;
        $meta['quick_replies']['user_' . $request->user()->id] = $request->quick_reply;
        $activity->update([
            'meta' => $meta,
        ]);

        event(new QuickReplyChanged($activity, $request->user()));
    }
}
