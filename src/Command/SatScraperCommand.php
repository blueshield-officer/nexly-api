<?php

namespace App\Command;

use App\Repository\ExternalSatRepository;
use App\Scraper\Sat\SatDatesParser;
use App\Scraper\Sat\SatPageFetcher;
use App\Scraper\Sat\SatSynchronizer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'FetchSatData',
    description: 'Add a short description for your command',
)]
class SatScraperCommand extends Command
{
    public function __construct(
        readonly SatPageFetcher $fetcher,
        readonly SatDatesParser $parser,
        readonly SatSynchronizer $synchronizer,
        readonly ExternalSatRepository $externalSatRepository
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $html   = $this->fetcher->fetchHtml();
        $dtos   = $this->parser->parse($html);
        $result = $this->synchronizer->sync($dtos);

        $io->success(sprintf(
            'Done — created: %d, updated: %d, skipped: %d',
            $result->created,
            $result->updated,
            $result->skipped,
        ));

        return Command::SUCCESS;
    }
}
