<?php

declare(strict_types=1);

namespace Denosys\Core\Commands;

use Denosys\Core\Application;
use Denosys\Core\Config\ConfigurationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'db:seed',
    description: 'Seed the database with records.'
)]
class DatabaseSeedCommand extends Command
{
    public function __construct(private readonly ConfigurationInterface $config, private readonly Application $core)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('class', null, InputOption::VALUE_OPTIONAL, 'The seeder class to run', 'DatabaseSeeder')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isProduction = $this->config->get('app.env') === 'production';

        if ($isProduction && !$input->getOption('force')) {
            $output->writeln('<error>Application is in production environment.</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>Seeding Database:</info> ' . $input->getOption('class'));

        $className = $input->getOption('class');
        $fullClassName = $this->core->getNamespace() . 'Database\\Seeders\\' . $className;

        if (!class_exists($fullClassName)) {
            $output->writeln("<error>Target class [$fullClassName] does not exist.</error>");
            return Command::FAILURE;
        }

        $seeder = new $fullClassName();
        $seeder->run();

        $output->writeln("<info>Done!</info>");

        return Command::SUCCESS;
    }
}
