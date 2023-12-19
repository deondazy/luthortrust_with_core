<?php

declare(strict_types=1);

namespace Denosys\App\Database\Factories;

use DateTime;
use Denosys\App\Database\Entities\Country;
use Denosys\App\Database\Entities\User;
use Denosys\Core\Database\Factories\Factory;

class UserFactory extends Factory
{
    protected string $entity = User::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional()->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'username' => $this->faker->unique()->userName(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'date_of_birth' => new DateTime($this->faker->date()),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'state' => $this->faker->countryCode(),
            'mobile_number' => $this->faker->phoneNumber(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'is_active' => $this->faker->boolean(),
            'roles' => ['ROLE_USER'],
            'pin' => $this->faker->numerify('####'),
            'status' => 'active',
            'country' => $this->getEntityManager()
                ->getReference(Country::class, $this->faker->biasedNumberBetween(1, 239)),
        ];
    }
}
