<?php

namespace Marshmallow\Comments\Resources;

use Marshmallow\Comments\Comments;
use Illuminate\Http\Resources\Json\JsonResource;

class NovaCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => Comments::getUserAvatar($this->user),
            ],
            'type' => [
                'id' => $this->type_key,
                'label' => $this->type_label,
            ],
            'comment' => nl2br($this->comment),
            'meta' => $this->meta,
            'other_quick_replies' => $this->getOtherQuickReplies($this->user),
            'time_ago' => $this->created_at->diffForHumans(),
        ];
        return parent::toArray($request);
    }
}
