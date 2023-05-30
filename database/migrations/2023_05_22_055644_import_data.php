<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ImportData extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        $file = storage_path('database/sql/init.sql');

        if (!file_exists($file)) {
            return;
        }

        DB::unprepared(file_get_contents($file));
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        // Define reverse logic here, if needed
    }
}
