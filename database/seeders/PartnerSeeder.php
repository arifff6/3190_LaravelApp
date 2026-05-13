<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 5; $i++) {
            DB::table('partners')->insert([
                'name' => $faker->company,
                'logo_url' => 'placehold.co' . urlencode($faker->word),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
