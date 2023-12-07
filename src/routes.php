<?php

use Carbon\Carbon;
use Laravel\Nova\Nova;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Marshmallow\Comments\Models\NovaComment;
use Marshmallow\Comments\Resources\NovaCommentsCollection;

Route::get('/{resourceName}/{resourceId}/get-comments', function ($resourceName, $resourceId, Request $request) {
    $resource = Nova::resourceForKey($resourceName);
    $model = $resource::newModel()->findOrFail($resourceId);

    return new NovaCommentsCollection(
        $model->novaComments
    );
});

Route::post('/{comment_id}/set-quick-reply', function ($comment_id, Request $request) {
    $comment = NovaComment::find($comment_id);
    $meta = $comment->meta;
    $meta['quick_replies']['user_' . $request->user()->id] = $request->quick_reply;
    $comment->update([
        'meta' => $meta,
    ]);
});

Route::post('/{resourceName}/{resourceId}', function ($resourceName, $resourceId, Request $request) {

    $resource = Nova::resourceForKey($resourceName);
    $model = $resource::newModel()->findOrFail($resourceId);

    try {

        $quick_replies = $request->quick_reply ? [
            'user_' . $request->user()->id => $request->quick_reply,
        ] : [];

        $model->novaComments()->create([
            'user_id' => $request->user()->id,
            'type_key' => $request->type,
            'type_label' => $request->type_label,
            'comment' => $request->comment,
            'created_at' => Carbon::parse($request->date)->setTimeFromTimeString(
                now()->format('H:i:s')
            ),
            'meta' => [
                'quick_replies' => $quick_replies,
            ],
        ]);

        return [
            'success' => true,
            'message' => 'Comment is created successfully',
        ];
    } catch (Exception $exception) {
        return [
            'success' => false,
            'message' => $exception->getMessage(),
        ];
    }
});
