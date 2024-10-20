<?php

namespace Database\Seeders;

use Artisan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShieldGenerateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::call('shield:generate --all');

        $this->command->info('All shields generated successfully.');
    }
}
