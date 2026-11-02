<?php

namespace App\Command;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:test',
    description: 'Test.',
)]
class TestCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em
    )
    {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $schoolNotification = new Notification();
        $schoolNotification->setPublishedAt(new \DateTimeImmutable());
        $schoolNotification->setTitle("Very good");
        $schoolNotification->setBody("Ohh yea, nothing today");
        $this->em->persist($schoolNotification);
        $this->em->flush();

        return Command::SUCCESS;
    }
}
