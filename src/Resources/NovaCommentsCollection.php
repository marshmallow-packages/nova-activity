<?php

namespace Marshmallow\Comments\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NovaCommentsCollection extends ResourceCollection
{
    public $collects = NovaCommentResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
