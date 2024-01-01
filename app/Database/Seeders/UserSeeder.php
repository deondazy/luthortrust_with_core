<?php

declare(strict_types=1);

namespace Denosys\App\Database\Seeders;

use Carbon\Carbon;
use DateTime;
use Denosys\App\Database\Entities\User;
use Denosys\App\Database\Entities\Country;

class UserSeeder
{
    public function run(): void
    {
        User::factory()->createMany(20000);

        $deon = User::factory()->create([
            'first_name' => 'Deon',
            'middle_name' => 'C',
            'last_name' => 'Okonkwo',
            'email' => 'deondazy@example.com',
            'username' => 'deondazy',
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => Carbon::make(fake()->date()),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'state' => fake()->countryCode(),
            'mobile_number' => fake()->phoneNumber(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'is_active' => fake()->boolean(),
            'roles' => ['ROLE_ADMIN'],
            'pin' => fake()->numerify('####'),
            'status' => 'active',
            'country' => entityManager()
                ->getReference(Country::class, fake()->biasedNumberBetween(1, 239)),
        ]);
    }
}
