<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('location')->insert([
            [
                'name' => 'Location A',
                'created_at' => now(),
                'created_by' => 1,
                'updated_at' => null,
                'updated_by' => null,
            ],
            [
                'name' => 'Location B',
                'created_at' => now(),
                'created_by' => 2,
                'updated_at' => null,
                'updated_by' => null,
            ],
            [
                'name' => 'Location C',
                'created_at' => now(),
                'created_by' => 3,
                'updated_at' => null,
                'updated_by' => null,
            ],
        ]);
    }
}
