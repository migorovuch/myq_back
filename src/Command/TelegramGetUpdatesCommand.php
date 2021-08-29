<?php

namespace App\Command;

use App\Model\Manager\BookingManagerInterface;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TelegramBot\Api\BotApi;

class TelegramGetUpdatesCommand extends Command
{
    protected static $defaultName = 'app:telegram:get-updates';
    protected static $defaultDescription = 'Add a short description for your command';

    public function __construct(
        protected BotApi $botApi,
        protected SerializerInterface $serializer,
        protected LoggerInterface $appLogger,
        protected BookingManagerInterface $bookingManager
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $updates = $this->botApi->getUpdates();
        $this->appLogger->info('Telegram bot updates', ['updates' => $updates]);

        return Command::SUCCESS;
    }
}
