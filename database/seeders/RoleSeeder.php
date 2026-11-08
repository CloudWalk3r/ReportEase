<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            1 => 'Admin',
            2 => 'Faculty Staff',
            3 => 'Maintenance Department',
            4 => 'Student',
        ];

        foreach ($roles as $id => $name) {
            DB::table('Roles')->updateOrInsert(
                ['role_id' => $id],
                ['role_name' => $name]
            );
        }
    }
}
