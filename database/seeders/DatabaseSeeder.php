<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // seed database with one admin user
        \App\Models\User::factory()->state(['name' => 'admin', 'password' => Hash::make('adminadmin'), 'email' => 'admin@email.com', 'isAdmin' => true])->create();
        \App\Models\User::factory(10)->create();

        \App\Models\Category::factory(5)->create();

        \App\Models\Card::factory(10)->create();

        \App\Models\Card_Category::factory(10)->create();

        \App\Models\Inventory::factory(10)->create();

        \App\Models\Discount::factory(10)->create();
    }
}
