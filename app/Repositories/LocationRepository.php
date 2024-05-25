<?php

namespace App\Repositories;

use App\Db\Core\Crud;
use App\Models\Country;
use App\Contracts\CountryInterface;
use App\Contracts\LocationInterface;
use App\Exceptions\CrudException;
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

    public function store(string $modelName, array $data,$folder_name = null , $tablename = null)
    {
        if (empty($data)) {
            throw CrudException::emptyData();
        }
        $model = app("App\Models\\{$modelName}");

        if(get_class($model) !== Config::get('variable.IMAGE_MODEL')){
            return  (new Crud($model, $data, null, false, false))->execute();
        }
        $crud = new Crud($model, $data, null, false, false);
        $crud->setImageDirectory($folder_name,$tablename);
        return $crud->execute();
    }

    public function update(string $modelName, array $data, int $id)
    {
        if (empty($data)) {
            throw CrudException::emptyData();
        }
        $model = app("App\Models\\{$modelName}");

        return (new Crud($model,$data,$id,true,false))->execute();
    }

    public function delete(string $modelName, int $id)
    {
        $model = app("App\Models\\{$modelName}");

        return (new Crud($model,null,$id,false,true))->execute();
    }
}
