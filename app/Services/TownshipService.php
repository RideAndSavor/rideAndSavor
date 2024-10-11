<?php
namespace App\Services;

use App\Contracts\TownshipInterface;

class TownshipService
{
    protected $townshipRepository;

    public function __construct(TownshipInterface $townshipRepository)
    {
        $this->townshipRepository = $townshipRepository;
    }

    public function getTownshipsByCity($city_id)
    {
        return $this->townshipRepository->getTownshipsByCity($city_id);
    }

}