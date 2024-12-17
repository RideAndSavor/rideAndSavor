<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface BaseInterface
{
  public function all();
  public function getById(int $id);
  public function getByName(string $name);
  public function getDataWithRelation(string $model);
  public function store(array $data, string $imageDir = null, string $diskName = null);
  public function twoModelsStore(int $id, string $relation, array $data);
  public function update(array $data, int $id,  string $imageDir = null, string $diskName = null);
  public function delete(int $id);
  public function morphStore(Model $parentModel, array $data, string $relationType);
}
