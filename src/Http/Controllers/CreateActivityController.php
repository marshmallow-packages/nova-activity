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

    public function storeNewActivity(Model $model, Request $request, string $key_prefixer = '')
    {
        try {
            $comment_validation = config('nova-activity.comment_validation');

            if ($key_prefixer) {
                foreach ($comment_validation as $attribute => $rules) {
                    unset($comment_validation[$attribute]);
                    $comment_validation[$key_prefixer . $attribute] = $rules;
                }
            }

            if ($comment_validation && !empty($comment_validation)) {
                $request->validate($comment_validation);
            }

            $attributes = [
                'comment', 'quick_reply', 'mentions',
                'type', 'type_label', 'date',
            ];

            foreach ($attributes as $attribute) {
                $variable_name = "{$attribute}_attribute";
                $$variable_name = "{$key_prefixer}{$attribute}";
                $$attribute = $request->{$$variable_name};
            }

            $user = $request->user();

            $quick_replies = $quick_reply ? [
                'user_' . $user->id => $quick_reply,
            ] : [];

            if ($mentions) {
                $mention_data = [];
                $comment = Str::of($comment);
                $available_mentions = is_array($mentions)
                    ? $mentions : json_decode($mentions, true);
                collect($available_mentions)->each(function ($available_mention) use (&$mention_data, $comment) {
                    if ($comment->contains("@{$available_mention['value']}")) {
                        $mention_data[] = $available_mention['model'];
                    }
                });
            }

            $activity = $model->addActivity(
                user_id: $user->id,
                type: $type,
                label: $type_label,
                comment: $comment,
                created_at: Carbon::parse($date)->setTimeFromTimeString(
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
