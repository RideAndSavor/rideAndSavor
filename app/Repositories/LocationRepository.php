<?php

namespace App\Repositories;

use App\DB\Core\Crud;
use App\Models\Country;
use App\Exceptions\CrudException;
use App\Contracts\CountryInterface;
use App\Contracts\LocationInterface;
use Illuminate\Support\Facades\Config;

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

    // public function findWhere($modelName, $link_id)
    // {
    //     $model = app("App\Models\\{$modelName}");
    //     // dd($model);
    //     return $model->where('link_id', $link_id)->get();
    // }

    public function findWhere($modelName, $id, $column = 'id')
{
    $model = app("App\Models\\{$modelName}");
    return $model->where($column, $id)->get();
}


    public function store(string $modelName, array $data, $folder_name = null, $tablename = null)
    {
        // dd($folder_name);
        // dd($data);
        // dd($_REQUEST);
        if (empty($data)) {
            // dd($data);
            throw CrudException::emptyData();
        }
        $model = app("App\\Models\\{$modelName}");
        // dd($model);

        if (get_class($model) !== Config::get('variable.IMAGE_MODEL')) {
            // dd($data);
            // dd($model);
            return (new Crud(model: $model,  data: $data, storeMode: true))->execute();
        }
        $crud = new Crud($model, $data, null, false, true);
        // dd($data);
        // dd($model);
        // dd(request());
        // dd($tablename);
        // dd($folder_name);
        $crud->setImageDirectory($folder_name,$tablename);
        // dd($crud->execute());
        return $crud->execute();
    }

    public function update(string $modelName, array $data, int $id, $folder_name = null, $tablename = null)
    {
        // dd($modelName);
        if (empty($data)) {
            throw CrudException::emptyData();
        }
        $model = app("App\Models\\{$modelName}");

        if (get_class($model) !== Config::get('variable.IMAGE_MODEL')) {
            return (new Crud($model, $data, $id, true, editMode:true))->execute();
        }
        $curd = new Crud($model, $data, $id, true,editMode:true);
        $curd->setImageDirectory($folder_name, $tablename);
        return $curd->execute();
    }

    public function delete(string $modelName, int $id)
    {

        $model = app("App\Models\\{$modelName}");
        // dd($model);

        return (new Crud($model, [], $id, false, deleteMode:true))->execute();
    }
}
