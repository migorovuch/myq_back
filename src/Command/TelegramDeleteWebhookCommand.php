<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception as TelegramException;

class TelegramDeleteWebhookCommand extends Command
{
    protected static $defaultName = 'app:telegram:delete-webhook';
    protected static $defaultDescription = 'Delete Webhook from telegram bot';

    /**
     * TelegramSetWebhookCommand constructor.
     *
     * @param BotApi $botApi
     */
    public function __construct(protected BotApi $botApi)
    {
        parent::__construct(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $this->botApi->deleteWebhook();

            $io->success('Telegram Webhook successfully deleted.');
        } catch (TelegramException $exception) {
            $io->error('Webhook delete error - '.$exception->getMessage());
        }

        return Command::SUCCESS;
    }
}
