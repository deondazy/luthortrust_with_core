<?php

declare(strict_types=1);

namespace Denosys\App\Database\Seeders;

use Denosys\App\Database\Entities\Country;
use Doctrine\ORM\EntityManagerInterface;

class CountrySeeder
{
    public function run(): void
    {
        $countryData = config('paths.storage_dir') . '/data/countries/countries.json';
        $entityManager = container(EntityManagerInterface::class);

        foreach (json_decode(file_get_contents($countryData), true) as $data) {
            $country = new Country();

            $country->setName($data['name'])
                    ->setIso($data['iso'])
                    ->setIso3($data['iso3'])
                    ->setNumCode($data['num_code'])
                    ->setPhoneCode($data['phone_code']);

            $entityManager->persist($country);
        }

        $entityManager->flush();
    }
}
