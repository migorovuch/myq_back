<?php

namespace App\Controller;

use App\Model\DTO\Telegram\UpdateDTO;
use App\Model\Manager\BotRequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TelegramController extends AbstractController
{
    /**
     * TelegramController constructor.
     */
    public function __construct(
        protected LoggerInterface $appLogger,
        protected BotRequestHandlerInterface $botRequestHandler
    ) {
    }

    /**
     * @Route("/telegram/{webhookToken}", name="api_telegram_webhook")
     * @ParamConverter("updateDTO", converter="fos_rest.request_body")
     */
    public function webhook(string $webhookToken, UpdateDTO $updateDTO): Response
    {
        $this->appLogger->info('Webhook data', ['data' => $updateDTO]);
        $this->botRequestHandler->handleRequest($webhookToken, $updateDTO);

        return new Response();
    }
}
