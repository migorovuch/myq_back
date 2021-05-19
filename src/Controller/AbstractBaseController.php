<?php

namespace App\Controller;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractBaseController extends AbstractFOSRestController
{
    /**
     * @param $data
     * @param int   $responseCode
     * @param array $serializationGroups
     *
     * @return Response
     */
    public function response($data, int $responseCode = Response::HTTP_OK, array $serializationGroups = [])
    {
        if (!empty($serializationGroups)) {
            $serializationGroups[] = 'Default';
        }
        $view = View::create($data, $responseCode);
        $context = new Context();
        $context->setGroups($serializationGroups);
        $view->setContext($context);

        return $this->handleView($view);
    }
}
