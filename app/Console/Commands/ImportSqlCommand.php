<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSqlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:import {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import an SQL file into the database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("SQL file '{$file}' does not exist.");
            return;
        }

        try {
            DB::unprepared(file_get_contents($file));
            $this->info('SQL file imported successfully.');
        } catch (\Exception $e) {
            $this->error('Error importing SQL file: ' . $e->getMessage());
        }
    }
}
