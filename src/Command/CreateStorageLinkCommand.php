<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:create-storage-link',
    description: 'Creates a symlink from public/storage to storage/public',
)]
class CreateStorageLinkCommand extends Command
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly Filesystem $filesystem,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectDir = $this->kernel->getProjectDir();

        $target = $projectDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'storage';
        $storage = $projectDir . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'public';

        if (!$this->filesystem->exists($storage)) {
            $output->writeln(sprintf('<error>Source directory does not exist: %s</error>', $storage));

            return Command::FAILURE;
        }

        if ($this->filesystem->exists($target)) {
            if (!is_link($target)) {
                $output->writeln(sprintf('<error>%s already exists and is not a symlink. Aborting to avoid deleting real data.</error>', $target));

                return Command::FAILURE;
            }

            $this->filesystem->remove($target);
        }

        $this->filesystem->symlink($storage, $target);

        $output->writeln(sprintf('Storage link created: %s -> %s', $target, $storage));

        return Command::SUCCESS;
    }
}
