<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        // 25 random tickets
        Ticket::factory()->count(25)->create();

        // Targeted subsets for filter testing
        Ticket::factory()->count(5)->open()->highPriority()->create();
        Ticket::factory()->count(5)->resolved()->create();
        Ticket::factory()->count(3)->unassigned()->create();
    }
}