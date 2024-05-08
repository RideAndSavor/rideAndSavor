<?php

namespace App\Repositories;
use App\Db\Core\Crud;
use App\Models\Country;
use App\Contracts\CountryInterface;

class CountryRepository implements CountryInterface
{
    public function all()
    {
        return Country::paginate(10);
    }

    public function findById(string $modelName, int $id)
    {
        $model = app("App\Models\\{$modelName}");
        return $model::find($id);
    }

    public function store(string $modelName, array $data)
    {
        return (new Crud(new Country(),$data,null,false,false))->execute();
    }

    public function update(string $modelName, array $data, int $id)
    {
        return (new Crud(new Country(),null,$id,true,false))->execute();
    }

    public function delete(string $modelName, int $id)
    {
        return (new Crud(new Country(),null,$id,false,true))->execute();
    }
}
