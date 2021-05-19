<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController
{
    /**
     * @return JsonResponse
     */
    #[Route(path: "api/v1/schedule", name: "create_schedule", methods: ["POST"])]
    public function create(): JsonResponse
    {
        return new JsonResponse(['OK']);
    }
}
