<?php

namespace App\Command;

use App\Enum\SisProviderEnum;
use App\Repository\SchoolRepository;
use App\Sis\Importer\Schedule\DemoScheduleImporter;
use App\Sis\Storage\Schedule\SchoolScheduleStorage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sis:import-schedules',
    description: 'Add a short description for your command',
)]
class SisImportSchedulesCommand extends Command
{
    public function __construct(
        private readonly SchoolRepository      $schoolRepository,

        private readonly DemoScheduleImporter  $demoScheduleImporter,
        private readonly SchoolScheduleStorage $scheduleStorage,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $schools = $this->schoolRepository->findAll();
        foreach ($schools as $school) {

            $schoolId = $school->getId();
            $schoolSis = $school->getSis()->getSisId();

            if ($schoolSis === SisProviderEnum::Demo) {
                // credential inserted
                $imported = $this->demoScheduleImporter->import();
                $this->scheduleStorage->upsert($schoolId, $imported);
            }

            // get some credentials....
        }

        $io->success('Good');
        return Command::SUCCESS;
    }
}
