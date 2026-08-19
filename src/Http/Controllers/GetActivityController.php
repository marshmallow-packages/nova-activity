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
        // Nova resolves detail/edit models without global scopes, so scoped
        // models (domain or state scopes) would 404 here if we kept them applied.
        $model = $resource::newModel()->newQueryWithoutScopes()->findOrFail($resourceId);

        return new NovaActivityCollection(
            $model->novaActivity()->with('user')->get()
        );
    }
}
