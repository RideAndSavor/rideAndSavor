<?php

namespace App\Contracts;

interface LocationInterface
{
    public function all(string $modelName);
    public function findById(string $modelName ,int $id);
    public function store(string $modelName ,array $data);
    public function update(string $modelName ,array $data,int $id);
    public function delete(string $modelName,int $id);
}
