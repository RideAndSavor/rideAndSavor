<?php

namespace App\Repositories;

use App\Contracts\TripInterface;

class TripRepository implements TripInterface
{
    public function findwhere($modelName, $user_id)
    {
        $model = app("App\Models\\{$modelName}");
        return $model->where('user_id', $user_id)->first();
    }

    public function findUser($modelName, $user_id)
    {
        $model = app("App\Models\\{$modelName}");
        return $model::findOrFail($user_id);
    }
}
