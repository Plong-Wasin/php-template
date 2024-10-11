<?php

namespace App\Console\Commands;

use App\Core\DB;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/** @package App\Console\Commands */
class MigrateCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "artisan")
            ->setName('migrate')

            // the short description shown while running "php artisan list"
            ->setDescription('Migrate command.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!DB::schema()->hasTable('migrations')) {
            DB::schema()->create('migrations', function (Blueprint $table) {
                $table->increments('id');
                $table->string('migration');
                $table->integer('batch');
            });
        }
        $maxBatch = DB::table('migrations')->max('batch');
        $batch = $maxBatch + 1;
        // scan file name in base_path('database/migrations')
        $files = scandir(base_path('database/migrations'));
        foreach ($files as $file) {
            if (strpos($file, '.php') !== false) {
                $class = require_once base_path('database/migrations/' . $file);
                if (class_exists($class)) {
                    $migration = new $class();
                    if ($migration->up()) {
                        DB::table('migrations')->insert(['migration' => $file, 'batch' => $batch]);
                        echo "Applied $file\n";
                    }
                }
            }
        }
    }
}
