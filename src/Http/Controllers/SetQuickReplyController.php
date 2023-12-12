<?php

namespace Marshmallow\NovaActivity\Http\Controllers;

use Illuminate\Http\Request;
use Marshmallow\NovaActivity\Models\NovaActivity;

class SetQuickReplyController
{
    public function __invoke($comment_id, Request $request)
    {
        $comment = NovaActivity::find($comment_id);
        $meta = $comment->meta;
        $meta['quick_replies']['user_' . $request->user()->id] = $request->quick_reply;
        $comment->update([
            'meta' => $meta,
        ]);
    }
}
