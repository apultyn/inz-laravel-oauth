<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'email' => 'apultyn@example.com',
            'keycloak_id' => '1b5b04e4-8400-4de9-86bb-4761f17cba77'
        ]);

        User::factory()->create([
            'email' => 'mpultyn@example.com',
            'keycloak_id' => 'e5a71a7f-0a34-4d4f-b9f9-78aa024a0db9'
        ]);

        User::factory()->create([
            'email' => 'bpultyn@example.com',
            'keycloak_id' => '214e8b44-bcbb-4960-9e4e-75fb582fd514'
        ]);
    }
}
