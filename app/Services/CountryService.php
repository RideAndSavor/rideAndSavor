<?php
namespace App\Services;
use App\Contracts\CountryInterface;

class CountryService
{
    protected $countryRepository;

    public function __construct(CountryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function getAllCountries()
    {
        return $this->countryRepository->getAllCountries();
    }
}
