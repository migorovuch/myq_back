<?php

namespace App\Controller;

use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TelegramController extends AbstractController
{
    /**
     * @Route("/telegram/{webhookToken}", name="api_telegram_webhook")
     */
    public function webhook(string $webhookToken, Request $request, LoggerInterface $logger, SerializerInterface $serializer): Response
    {
        $data = $serializer->serialize($request->request->all(), 'json');
        $logger->info('Webhook data', ['data' => $data]);

        return new Response();
    }
}
