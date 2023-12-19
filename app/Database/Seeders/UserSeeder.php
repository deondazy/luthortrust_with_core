<?php

declare(strict_types=1);

namespace Denosys\App\Database\Seeders;

use Denosys\App\Database\Entities\User;

class UserSeeder
{
    public function run(): void
    {
        $start = microtime(true);
        $count = 50000;
        User::factory()->createMany($count);
        $end = microtime(true);
        $time = $end - $start;
        echo ("Seeding $count records completed in $time seconds. \n");
    }
}
