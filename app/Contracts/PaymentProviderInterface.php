<?php

namespace App\Contracts;

interface PaymentProviderInterface
{
    public function all(string $modelName);
    public function findByID(string $modelName, int $id);
    public function store(string $modelName, array $data);
    public function update(string $modelName, array $data, int $id);
    public function delete(string $modelName, int $id);
}
