<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Statuses;

class StatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $user_statuses = [
            ['name' => 'Active', 'table' => 'user', 'bg_color' => '#28a745'],
            ['name' => 'Inactive', 'table' => 'user', 'bg_color' => '#dc3545'],
            ['name' => 'Pending', 'table' => 'user', 'bg_color' => '#ffc107'],
            ['name' => 'Suspended', 'table' => 'user', 'bg_color' => '#6c757d'],
            ['name' => 'Deleted', 'table' => 'user', 'bg_color' => '#343a40'],
        ];
        for($i = 0; $i < 5; $i++) {
            Statuses::create($user_statuses[$i]);
        }
    }
}
