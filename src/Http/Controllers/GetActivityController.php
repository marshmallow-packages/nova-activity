<?php

namespace Marshmallow\NovaActivity\Http\Controllers;

use Laravel\Nova\Nova;
use Illuminate\Http\Request;
use Marshmallow\NovaActivity\Resources\NovaActivityCollection;

class GetActivityController
{
    public function __invoke($resourceName, $resourceId, Request $request)
    {
        $resource = Nova::resourceForKey($resourceName);
        $model = $resource::newModel()->findOrFail($resourceId);

        return new NovaActivityCollection(
            $model->novaActivity
        );
    }
}
