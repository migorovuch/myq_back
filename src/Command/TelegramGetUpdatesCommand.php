<?php

namespace App\Command;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TelegramBot\Api\BotApi;

class TelegramGetUpdatesCommand extends Command
{
    protected static $defaultName = 'app:telegram:get-updates';
    protected static $defaultDescription = 'Add a short description for your command';

    public function __construct(protected BotApi $botApi, protected SerializerInterface $serializer)
    {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
//        $this->botApi->sendMessage(326053432, 'some message 123');
        $updates = $this->botApi->getUpdates(489038434);
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
