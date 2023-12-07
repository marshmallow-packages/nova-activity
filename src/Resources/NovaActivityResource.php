<?php

namespace Marshmallow\NovaActivity\Resources;

use Marshmallow\NovaActivity\Activity;
use Illuminate\Http\Resources\Json\JsonResource;

class NovaActivityResource extends JsonResource
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
                'avatar' => Activity::getUserAvatar($this->user),
            ],
            'type' => [
                'id' => $this->type_key,
                'label' => $this->type_label ?? '',
            ],
            'comment' => nl2br($this->comment),
            'meta' => $this->meta,
            'other_quick_replies' => $this->getOtherQuickReplies($this->user),
            'time_ago' => $this->created_at->diffForHumans(),
            'is_starred' => $this->is_starred,
            'is_pinned' => $this->is_pinned,
            'is_hidden' => $this->is_hidden,
            'is_resolved' => $this->is_resolved,
        ];
        return parent::toArray($request);
    }
}
