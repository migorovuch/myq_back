<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception as TelegramException;

class TelegramSetWebhookCommand extends Command
{
    protected static $defaultName = 'app:telegram:set-webhook';
    protected static $defaultDescription = 'Add Webhook to telegram bot';

    /**
     * TelegramSetWebhookCommand constructor.
     *
     * @param BotApi $botApi
     */
    public function __construct(protected BotApi $botApi)
    {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('url', InputArgument::REQUIRED, 'Webhook URL');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $webhookURL = $input->getArgument('url');
        if ($webhookURL) {
            $io->note(sprintf('You passed Webhook URL: %s', $webhookURL));
        }
        try {
            $result = '';
            $webhookInfo = $this->botApi->getWebhookInfo();
            if (($existWebhookURL = $webhookInfo->getUrl())) {
                if ($io->ask('Telegram webhook already configured - '.$existWebhookURL.\PHP_EOL.'Do you want to change it?')) {
                    $result = $this->botApi->setWebhook($webhookURL);
                }
            } else {
                $result = $this->botApi->setWebhook($webhookURL);
            }

            $io->success('Webhook configured successfully '.$result);
        } catch (TelegramException $exception) {
            $io->error('Set Webhook error - '.$exception->getMessage());
        }

        return Command::SUCCESS;
    }
}
