<?php

namespace Marshmallow\NovaActivity\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Laravel\Nova\Nova;
use Illuminate\Http\Request;

class CreateActivityController
{
    public function __invoke($resourceName, $resourceId, Request $request)
    {
        $resource = Nova::resourceForKey($resourceName);
        $model = $resource::newModel()->findOrFail($resourceId);

        try {

            $comment_validation = config('nova-activity.comment_validation');
            if ($comment_validation && !empty($comment_validation)) {
                $request->validate($comment_validation);
            }

            $quick_replies = $request->quick_reply ? [
                'user_' . $request->user()->id => $request->quick_reply,
            ] : [];

            $model->addActivity(
                user_id: $request->user()->id,
                type: $request->type,
                label: $request->type_label,
                comment: $request->comment,
                created_at: Carbon::parse($request->date)->setTimeFromTimeString(
                    now()->format('H:i:s')
                ),
                quick_replies: $quick_replies,
            );

            return [
                'success' => true,
                'message' => __('Comment is created successfully'),
            ];
        } catch (Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
        }
    }
}
