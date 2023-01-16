<?php

namespace App\Controller;

use App\Service\AWS\InstanceMetadataService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("/aws", name="api_aws_")
 */
class AWSController extends AbstractBaseController
{
    public function __construct(protected InstanceMetadataService $instanceMetadataService)
    {
    }

    /**
     * @Rest\Get("/ii/app", name="ii")
     * @return Response
     */
    public function instanceIdentity(): Response
    {
        return $this->response($this->instanceMetadataService->getInstanceIdentity());
    }
}