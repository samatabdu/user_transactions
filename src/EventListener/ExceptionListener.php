<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class ExceptionListener
{
  public function onKernelException(ExceptionEvent $event)
  {
    $exception = $event->getThrowable();

    if ($exception instanceof NotFoundHttpException) {
      $response = new JsonResponse([
        'error' => 'Route not found',
        'message' => 'The requested endpoint does not exist',
      ], JsonResponse::HTTP_NOT_FOUND);

      $event->setResponse($response);
    }

//    if ($exception instanceof ResourceNotFoundException) {
//      $response = new JsonResponse([
//        'error' => 'Route not found',
//        'message' => 'The requested endpoint does not exist',
//      ], JsonResponse::HTTP_NOT_FOUND);
//      $event->setResponse($response);
//    }
//
//    if ($exception instanceof MethodNotAllowedException) {
//      $response = new JsonResponse([
//        'error' => 'Method not allowed',
//        'message' => 'Check allowed HTTP methods for this endpoint',
//      ], JsonResponse::HTTP_METHOD_NOT_ALLOWED);
//      $event->setResponse($response);
//    }
  }
}