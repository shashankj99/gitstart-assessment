<?php

namespace App\EventListener;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        switch ($exception) {
            case $exception instanceof EntityNotFoundException:
                $response = new JsonResponse([
                    'message' => $exception->getMessage(),
                ], JsonResponse::HTTP_NOT_FOUND);
                break;

            case $exception instanceof UnprocessableEntityHttpException:
                $errors = [];
                foreach ($exception->getPrevious()->getViolations() as $violation) {
                    $errors[] = [
                        'field' => $violation->getPropertyPath(),
                        'message' => $violation->getMessage(),
                    ];
                }

                $response = new JsonResponse([
                    'message' => 'Unprocessable Entity',
                    'errors' => $errors,
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                break;
            
            default:
                $response = new JsonResponse([
                    'message' => 'Internal Server Error',
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
                break;
        }

        $event->setResponse($response);
    }
}
