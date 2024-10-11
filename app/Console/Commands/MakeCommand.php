<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "app/console")
            ->setName('make:command')
            ->addArgument('filename', InputArgument::REQUIRED, 'The command file name')

            // the short description shown while running "php app/console list"
            ->setDescription('Creates a new command.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command to create command');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandFilename = $input->getArgument('filename');
        $filename = $commandFilename . '.php';
        $content = <<<EOT
<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class $commandFilename extends Command
{
    protected function configure()
    {
        \$this
            // the name of the command (the part after "artisan")
            ->setName('make:command')
            ->addArgument('filename', InputArgument::REQUIRED, 'The command file name')

            // the short description shown while running "php artisan list"
            ->setDescription('Creates a new command.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command to create command');
    }

    protected function execute(InputInterface \$input, OutputInterface \$output)
    {
        \$argument = \$input->getArgument('argument');
        
    }
}
EOT;

        $path = base_path('/app/Console/Commands/' . $filename);
        file_put_contents($path, $content);
        echo "Created $path\n";
    }
}
