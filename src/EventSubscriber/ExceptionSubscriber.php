<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class ExceptionSubscriber implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();
        $exceptionData = [
            'success' => false,
            'error' => [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ],
        ];

        $statusCode = $exception->getCode();

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }

        if ($statusCode < 400 && $statusCode > 599) {
            $statusCode = 500;
        }

        $this->logger->error($exception->getMessage());
        $response = new JsonResponse($exceptionData, $statusCode);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
