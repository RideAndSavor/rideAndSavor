<?php
namespace App\Services;

use App\Contracts\StateInterface;

class StateService
{
    protected $stateRepository;

    public function __construct(StateInterface $stateRepository)
    {
        $this->stateRepository = $stateRepository;
    }

    public function getStatesByCountry($country_id)
    {
        return $this->stateRepository->getStatesByCountry($country_id);
    }
}
