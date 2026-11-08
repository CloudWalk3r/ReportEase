<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sections = [
            1 => ['name' => 'Electrical Maintenance', 'description' => 'Handles electrical systems and wiring'],
            2 => ['name' => 'Mechanical Maintenance', 'description' => 'Responsible for machinery and equipment'],
            3 => ['name' => 'Civil Works', 'description' => 'Building infrastructure and structural repairs'],
            4 => ['name' => 'General Maintenance', 'description' => 'General facility upkeep and repairs'],
        ];

        foreach ($sections as $id => $section) {
            DB::table('Section')->updateOrInsert(
                ['section_id' => $id],
                ['section_name' => $section['name'], 'description' => $section['description']]
            );
        }
    }
}
