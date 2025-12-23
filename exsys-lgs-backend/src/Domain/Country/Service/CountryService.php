<?php

namespace App\Domain\Country\Service;

use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepository;

class CountryService
{
    public function __construct(
        private CountryRepository $countryRepository
    ) {}

    /**
     * Get all countries
     */
    public function getAllActiveCountries(): array
    {
        $countries = $this->countryRepository->findAllActive();
        
        return array_map(function(Country $country) {
            return [
                'id' => $country->getId()->toString(),
                'code' => $country->getCode(),
                'name' => $country->getName(),
                'nationality' => $country->getNationality()
            ];
        }, $countries);
    }

    /**
     * Get all nationalities from countries
     */
    public function getAllNationalities(): array
    {
        $countries = $this->countryRepository->findAllActive();
        
        return array_map(function(Country $country) {
            return [
                'value' => strtolower($country->getNationality()),
                'label' => $country->getNationality(),
                'countryId' => $country->getId()->toString(),
                'countryName' => $country->getName()
            ];
        }, $countries);
    }

    /**
     * Find country by nationality (used for migration)
     */
    public function findByNationality(string $nationality): ?Country
    {
        return $this->countryRepository->findByNationality($nationality);
    }

    /**
     * Find country by code
     */
    public function findByCode(string $code): ?Country
    {
        return $this->countryRepository->findByCode($code);
    }
}
