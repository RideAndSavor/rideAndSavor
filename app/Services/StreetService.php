<?php
namespace App\Services;

use App\Contracts\StreetInterface;

class StreetService
{
    protected $streetRepository;

    public function __construct(StreetInterface $streetRepository)
    {
        $this->streetRepository = $streetRepository;
    }

    public function getStreetsByWard($ward_id)
    {
        return $this->streetRepository->getStreetsByWard($ward_id);
    }
}
