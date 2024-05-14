<?php

namespace App\Repositories;

use App\Db\Core\Crud;
use App\Models\Country;
use App\Contracts\CountryInterface;
use App\Contracts\LocationInterface;

class LocationRepository implements LocationInterface
{
    public function findByIdWithRelation(string $modelName, string $relationName, int $id)
    {
        $model = app("App\Models\\{$modelName}");
        return $model::with($relationName)->findOrFail($id);
    }

    public function relationData($modelName, $relationName)
    {
        $model = app("App\Models\\{$modelName}");
        return $model::with($relationName)->paginate();
    }

    public function all(string $modelName)
    {
        $model = app("App\Models\\{$modelName}");
        return $model::paginate(10);
    }

    public function findById(string $modelName, int $id)
    {
        $model = app("App\Models\\{$modelName}");
        return $model::find($id);
    }

    public function store(string $modelName, array $data)
    {
        $model = app("App\Models\\{$modelName}");
        return (new Crud($model, $data, null, false, false))->execute();
    }

    public function update(string $modelName, array $data, int $id)
    {
        $model = app("App\Models\\{$modelName}");

        return (new Crud($model, $data, $id, true, false))->execute();
    }

    public function delete(string $modelName, int $id)
    {
        $model = app("App\Models\\{$modelName}");

        return (new Crud($model, null, $id, false, true))->execute();
    }
}
