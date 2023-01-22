<?php

namespace App\Command;

use App\Message\PodcastsUpdateMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:update-podcasts',
    description: 'Add a short description for your command',
)]
#[AsCronJob('0 * * * *')]
class UpdatePodcastsCommand extends Command
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription("triggers update to update all saved podcasts");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->bus->dispatch(new PodcastsUpdateMessage());

        $io->success('Message sent to to update all episodes');

        return Command::SUCCESS;
    }
}
