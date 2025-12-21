<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['superadmin', 'admin', 'receptionist', 'customer'];
        $now = Carbon::now();

        DB::table('countries')->insertOrIgnore([
            'name' => 'Hungary',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $countryId = DB::table('countries')->where('name', 'Hungary')->value('id');

        DB::table('cities')->insertOrIgnore([
            'name' => 'Budapest',
            'country_id' => $countryId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $cityId = DB::table('cities')->where('name', 'Budapest')->value('id');

        DB::table('addresses')->insertOrIgnore([
            'postal_code' => '1000',
            'address' => 'Main Street 1',
            'city_id' => $cityId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $addressId = DB::table('addresses')->where('address', 'Main Street 1')->value('id');

        foreach ($roles as $role) {
            DB::table('users')->insert([
                'name' => $role,
                'email' => "$role@myroomify.com",
                'password' => Hash::make('password123'),
                'role' => $role,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $userId = DB::table('users')->where('email', "$role@myroomify.com")->value('id');

            DB::table('profiles')->insert([
                'user_id' => $userId,
                'first_name' => ucfirst($role),
                'last_name' => 'Test',
                'phone' => '0612345678',
                'address_id' => $addressId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
