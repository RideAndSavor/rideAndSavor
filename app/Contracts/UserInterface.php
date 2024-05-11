<?php

namespace App\Contracts;

interface UserInterface
{
    public function store(string $modelName, array $data);
}
