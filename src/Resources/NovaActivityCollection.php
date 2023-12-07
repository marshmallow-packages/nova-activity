<?php

namespace Marshmallow\NovaActivity\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NovaActivityCollection extends ResourceCollection
{
    public $collects = NovaActivityResource::class;

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
