<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);

        $faker = Faker::create();

        $buildings = [];
        for ($i = 1; $i <= 20; $i++) {
            $buildings[] = DB::table('buildings')->insertGetId([
                'address' => $faker->address,
                'latitude' => $faker->randomFloat(6, -90, 90), // Генерация с ограничением [-90, 90]
                'longitude' => $faker->randomFloat(6, -180, 180), // Генерация с ограничением [-180, 180]
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $organizations = [];
        for ($i = 1; $i <= 20; $i++) {
            $organizations[] = DB::table('organizations')->insertGetId([
                'name' => $faker->company,
                'phone_numbers' => json_encode([$faker->phoneNumber, $faker->phoneNumber]),
                'building_id' => $faker->randomElement($buildings),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $activities = [];
        for ($i = 1; $i <= 20; $i++) {
            $activities[] = DB::table('activities')->insertGetId([
                'name' => $faker->jobTitle,
                'parent_id' => $faker->optional()->randomElement($activities),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($organizations as $organization) {
            DB::table('organization_activity')->insert([
                'organization_id' => $organization,
                'activity_id' => $faker->randomElement($activities),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
