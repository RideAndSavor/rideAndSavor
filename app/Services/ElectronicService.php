<?php

namespace App\Services;

use App\Contracts\ElectronicInterface;

use App\Models\Electronic;

class ElectronicService
{
    protected $electronicRepository;

    public function __construct(ElectronicInterface $electronicRepository)
    {
        $this->electronicRepository = $electronicRepository;
    }

    public function getAllElectronics()
    {
        // dd("ok");
        // dd($this->electronicRepository->all());
        return $this->electronicRepository->all();
    }

    public function store(array $data): Electronic
    {
        return $this->electronicRepository->store($data);
    }

    public function update(array $data, int $id)
    {
        return $this->electronicRepository->update($data, $id);
    }

    public function delete(int $id)
    {
        $this->electronicRepository->delete($id);
    }


}
