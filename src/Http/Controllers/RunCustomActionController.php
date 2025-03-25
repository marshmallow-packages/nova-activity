<?php

namespace Marshmallow\NovaActivity\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Marshmallow\NovaActivity\Models\NovaActivity;

class RunCustomActionController
{
    public function __invoke($activity_id, Request $request)
    {
        $comment = NovaActivity::find($activity_id);
        $action = json_decode($request->action, true);
        $class = Arr::get($action, 'class');

        $class::handle(
            $comment,
            $action,
        );

        return response()->json([
            'status' => 'success',
        ]);
    }
}
