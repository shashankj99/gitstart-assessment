<?php

namespace App\EventListener;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        switch ($exception) {
            case $exception instanceof EntityNotFoundException:
                $response = new JsonResponse([
                    'message' => $exception->getMessage(),
                ], JsonResponse::HTTP_NOT_FOUND);
                break;

            case $exception instanceof UnprocessableEntityHttpException:
                $previous = $exception->getPrevious();
                if ($previous instanceof ValidationFailedException) {
                    $errors = [];
                    foreach ($previous->getViolations() as $violation) {
                        $errors[] = [
                            'field' => $violation->getPropertyPath(),
                            'message' => $violation->getMessage(),
                        ];
                    }

                    $response = new JsonResponse([
                        'message' => 'Unprocessable Entity',
                        'errors' => $errors,
                    ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                } else {
                    $response = new JsonResponse([
                        'message' => $exception->getMessage(),
                    ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
                break;

            case $exception instanceof HttpException:
                $response = new JsonResponse([
                    'message' => $exception->getMessage(),
                ], $exception->getStatusCode());
                break;

            default:
                $response = new JsonResponse([
                    'message' => $exception->getMessage(),
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
                break;
        }

        $event->setResponse($response);
    }
}
