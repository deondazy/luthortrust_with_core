<?php

declare(strict_types=1);

namespace Denosys\Core\Commands;

use Denosys\Core\Encryption\Encrypter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Denosys\Core\Config\ConfigurationInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'key:generate',
    description: 'Generate encryption key.'
)]
class GenerateEncryptionKeyCommand extends Command
{
    public function __construct(private readonly ConfigurationInterface $config, private ?string $error = null)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('show', 's', null, 'Display the key instead of modifying files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = $this->generateKey();

        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);

        if ($input->getOption('show')) {
            $output->writeln('<comment>' . $key . '</comment>');
            return Command::SUCCESS;
        }

        if (!$this->writeKey($key)) {
            $io->error($this->error);
            return Command::FAILURE;
        }

        $io->success('Encryption key generated and saved successfully.');

        return Command::SUCCESS;
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function generateKey(): string
    {
        return Encrypter::generateKey();
    }

    /**
     * Write the key to the environment file.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function writeKey(string $key): bool
    {
        if (!file_exists($this->envFile())) {
            $this->error = 'No .env file present. Unable to set APP_KEY.';
            return false;
        }

        $replaced = preg_replace(
            $this->keyPattern(),
            "APP_KEY=$key",
            $input = file_get_contents($this->envFile())
        );

        if ($replaced === $input || $replaced === null) {
            $this->error = 'No APP_KEY variable found in .env file. Unable to set APP_KEY.';
            return false;
        }

        file_put_contents($this->envFile(), $replaced);

        return true;
    }

    /**
     * Get regex pattern for matching the APP_KEY in the environment file.
     *
     * @return string
     */
    protected function keyPattern(): string
    {
        $escaped = preg_quote('=' . $this->config->get('app.key'), '/');
        return "/^APP_KEY$escaped/m";
    }

    /**
     * Get the environment file.
     *
     * @return string|int
     */
    protected function envFile(): string|int
    {
        return $this->config->get('paths.root_dir') . '/.env';
    }
}
