<?php

namespace App\Contracts;

interface LocationInterface
{
    public function findByIdWithRelation(string $modelName, string $relationName, int $id);
    public function relationData(string $modelName, string $relationName);
    public function all(string $modelName);
    // public function findWhere($modelName, $link_id);
    public function findWhere($modelName, $id, $column = 'id');
    public function findById(string $modelName, int $id);
    public function store(string $modelName, array $data, $folder_name = null, $tablename = null);
    public function update(string $modelName, array $data, int $id, $folder_name = null, $tablename = null);
    public function delete(string $modelName, int $id);
}
