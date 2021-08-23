<?php

namespace App\EventSubscriber;

use App\Exception\ApiExceptionInterface;
use App\Exception\ValidationFailedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Throwable;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ApiExceptionSubscriber.
 */
class ApiExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;
    protected TranslatorInterface $translator;
    protected string $appEnv;

    /**
     * ApiExceptionSubscriber constructor.
     *
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param string $appEnv
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator, string $appEnv)
    {
        $this->logger = $logger;
        $this->appEnv = $appEnv;
        $this->translator = $translator;
    }

    /**
     * @param ExceptionEvent $event
     *
     * @return ExceptionEvent
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $code = $exception->getCode();
        $response = $exceptionContext = [];
//        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if ($exception instanceof ValidationFailedException) {
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;
            $errorsList = $exception->getConstraintViolationList();
            $responseErrors = [];
            foreach ($errorsList as $error) {
                $responseErrors[] = [
                    'source' => $error->getPropertyPath(),
                    'title' => $error->getMessage(),
                ];
            }
            $exceptionContext = $responseErrors;
            $response = [
                'title' => $this->translator->trans('Validation failed with %count% error(s).', ['%count%' => count($responseErrors)]),
                'errors' => $responseErrors,
            ];
        } elseif ($exception instanceof ApiExceptionInterface) {
            $code = $code ?: Response::HTTP_BAD_REQUEST;
            $response = [
                'title' => $exception->getMessage(),
            ];
        } elseif ($exception instanceof AuthenticationCredentialsNotFoundException) {
            $code = Response::HTTP_UNAUTHORIZED;
            $response = [
                'title' => $this->translator->trans('Authentication failed!'),
            ];
        } elseif ($exception instanceof NotFoundHttpException) {
            $code = Response::HTTP_NOT_FOUND;
            $response = [
                'title' => $this->translator->trans('Resource not found'),
            ];
        } else {
            $code = $code ?: Response::HTTP_INTERNAL_SERVER_ERROR;
            $response = [
                'title' => $this->appEnv === 'dev' ? $exception->getMessage() : $this->translator->trans('Ooops something went wrong!'),
            ];
        }
        $event->setResponse(
            new JsonResponse($response, $code)
        );
        $this->log($exception, $exceptionContext);

        return $event;
    }

    /**
     * @param Throwable $exception
     * @param array $exceptionContext
     */
    private function log(Throwable $exception, array $exceptionContext = [])
    {
        $log = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'exceptionContext' => $exceptionContext,
            'occurred' => [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ],
            'trace' => $exception->getTrace()
        ];
        if (isset($exception->getTrace()[0]['file'])) {
            $log['called']['file'] = $exception->getTrace()[0]['file'];
        }
        if (isset($exception->getTrace()[0]['line'])) {
            $log['called']['line'] = $exception->getTrace()[0]['line'];
        }

        if ($exception->getPrevious() instanceof Throwable) {
            $log += [
                'previous' => [
                    'message' => $exception->getPrevious()->getMessage(),
                    'exception' => get_class($exception->getPrevious()),
                    'file' => $exception->getPrevious()->getFile(),
                    'line' => $exception->getPrevious()->getLine(),
                ],
            ];
        }

        $this->logger->error($exception->getMessage(), $log);
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 6],
        ];
    }
}
