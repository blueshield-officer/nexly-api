<?php

namespace App\Command;

use App\Service\HealthChecker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:healthcheck',
    description: 'Checks database and Redis connectivity.',
)]
class HealthCheckCommand extends Command
{
    public function __construct(
        private readonly HealthChecker $healthChecker,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $failed = array_keys(array_filter($this->healthChecker->check(), fn (bool $ok) => !$ok));

        if ($failed) {
            $output->writeln('<error>Unhealthy: ' . implode(', ', $failed) . '</error>');

            return Command::FAILURE;
        }

        $output->writeln('<info>OK - Database and Redis are healthy.</info>');

        return Command::SUCCESS;
    }
}
