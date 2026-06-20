<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/wilayah.sql');
        if (File::exists($path)) {
            $this->command->info('Importing wilayah.sql to database... (this may take a few seconds)');
            DB::unprepared(File::get($path));
            $this->command->info('Importing completed!');
        } else {
            $this->command->error('File wilayah.sql not found at ' . $path);
        }
    }
}
