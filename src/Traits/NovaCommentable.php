<?php

namespace Marshmallow\Comments\Traits;

use Marshmallow\Comments\Models\NovaComment;

trait NovaCommentable
{
    public function novaComments()
    {
        return $this->morphMany(NovaComment::class, 'nova_commentable')->orderBy('created_at');
    }
}
