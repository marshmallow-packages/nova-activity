<?php

namespace Marshmallow\NovaActivity\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Laravel\Nova\Nova;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Marshmallow\NovaActivity\Events\ActivityCreated;

class CreateActivityController
{
    public function __invoke($resourceName, $resourceId, Request $request)
    {
        $resource = Nova::resourceForKey($resourceName);
        $model = $resource::newModel()->findOrFail($resourceId);
        return $this->storeNewActivity($model, $request);
    }

    public function storeNewActivity(Model $model, Request $request)
    {
        try {
            $comment_validation = config('nova-activity.comment_validation');
            if ($comment_validation && !empty($comment_validation)) {
                $request->validate($comment_validation);
            }

            $quick_replies = $request->quick_reply ? [
                'user_' . $request->user()->id => $request->quick_reply,
            ] : [];

            if ($request->mentions) {
                $mention_data = [];
                $comment = Str::of($request->comment);
                $available_mentions = is_array($request->mentions) ? $request->mentions : json_decode($request->mentions, true);
                collect($available_mentions)->each(function ($available_mention) use (&$mention_data, $comment) {
                    if ($comment->contains("@{$available_mention['value']}")) {
                        $mention_data[] = $available_mention['model'];
                    }
                });
            }

            $activity = $model->addActivity(
                user_id: $request->user()->id,
                type: $request->type,
                label: $request->type_label,
                comment: $request->comment,
                created_at: Carbon::parse($request->date)->setTimeFromTimeString(
                    now()->format('H:i:s')
                ),
                quick_replies: $quick_replies,
                mentions: $mention_data,
            );

            event(new ActivityCreated($activity));

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
