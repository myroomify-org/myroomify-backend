<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        for ($i = 1; $i <= 20; $i++) {
            DB::table('rooms')->insert([
                'name' => "Room $i",
                'capacity' => rand(1, 4),
                'description' => "Description for Room $i",
                'price' => rand(50, 200),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
