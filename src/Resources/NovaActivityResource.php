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
                'id' => $this->user?->id ?? null,
                'name' => $this->user?->name ?? __('System'),
                'avatar' => Activity::getUserAvatar($this->user),
                'icon' => $this->user ? null : 'desktop-computer',
            ],
            'type' => [
                'id' => $this->type_key,
                'label' => $this->type_label ?? '',
            ],
            'comment' => nl2br($this->comment),
            'meta' => $this->meta,
            'other_quick_replies' => $this->getOtherQuickReplies(),
            'created_at' => $this->created_at->format(
                config('nova-activity.dates.format')
            ),
            'time_ago' => $this->created_at->diffForHumans(),
            'is_starred' => $this->is_starred,
            'is_pinned' => $this->is_pinned,
            'is_hidden' => $this->is_hidden,
            'is_resolved' => $this->is_resolved,
        ];
        return parent::toArray($request);
    }
}
