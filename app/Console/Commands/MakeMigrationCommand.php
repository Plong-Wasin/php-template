<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMigrationCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "app/console")
            ->setName('make:migration')
            ->addArgument('name', InputArgument::REQUIRED, 'The migration name')

            // the short description shown while running "php app/console list"
            ->setDescription('Creates a new migration.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command to create migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrationName = $input->getArgument('name');
        $filename = date('Y_m_d_His') . '_' . $migrationName . '.php';
        $path = base_path('/database/migrations/' . $filename);
        $content = <<<EOT
<?php

use App\Core\DB;
use Illuminate\Database\Schema\Blueprint;

return new class
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::schema()->create('', function (Blueprint \$table) {

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::schema()->drop('');
    }
};
EOT;

        file_put_contents($path, $content);
        echo "Created $path\n";
    }
}
