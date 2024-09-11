<?php

namespace App\Repositories;

use App\Contracts\PaymentProviderInterface;
use App\DB\Core\Crud;
use App\Models\PaymentProvider;


class PaymentProviderRepository implements PaymentProviderInterface
{

    public function all($modelName)
    {
        $model = app("App\\Models\\{$modelName}");
        return $model::paginate(10);
    }

    public function findByID(string $modelName, int $id)
    {
        $model = app("App\Models\\{$modelName}");
        return $model::find($id);
    }

    public function store(string $modelName, array $data)
    {
        $model = app("App\\Models\\{$modelName}");
        return (new Crud($model, $data, null, false, false))->execute();
    }

    public function update(string $modelName, array $data, int $id)
    {
        return (new Crud(new PaymentProvider(), $data, $id, true, false))->execute();
    }

    public function delete(string $modelName, int $id)
    {
        return (new Crud(new PaymentProvider(), null, $id, false, true))->execute();
    }
}
