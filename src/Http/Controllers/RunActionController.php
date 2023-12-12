<?php

namespace Marshmallow\NovaActivity\Http\Controllers;

use Illuminate\Http\Request;
use Marshmallow\NovaActivity\Models\NovaActivity;

class RunActionController
{
    public function __invoke($activity_id, Request $request)
    {
        $comment = NovaActivity::find($activity_id);
        $comment->runAction($request->action);
    }
}
