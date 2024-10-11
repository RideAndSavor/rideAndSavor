<?php
namespace App\Services;

use App\Contracts\CityInterface;

class CityService 
{
    protected $cityRepository;

    public function __construct(CityInterface $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function getCitiesByState($state_id)
    {
        return $this->cityRepository->getCitiesByState($state_id);
    }
}