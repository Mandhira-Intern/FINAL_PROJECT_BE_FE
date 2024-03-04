<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i = 0; $i < 100; $i++)
        {
            Event::create([
                'event_name' => 'Event ' . $i,
                'start_date' => fake()->date('Y-m-d'),
                'end_date' => fake()->date('Y-m-d'),
                'description' => 'Description ' . $i,
            ]);
        }
    }
}
