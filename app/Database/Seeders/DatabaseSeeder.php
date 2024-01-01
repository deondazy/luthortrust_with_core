<?php

declare(strict_types=1);

namespace Denosys\App\Database\Seeders;

class DatabaseSeeder
{
    public function run(): void
    {
        (new CountrySeeder())->run();
        (new UserSeeder())->run();
    }
}
