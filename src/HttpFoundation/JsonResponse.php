<?php

namespace AKlump\AnnotatedResponse\HttpFoundation;

use AKlump\AnnotatedResponse\AnnotatedResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse as SymfonyJsonResponse;


/**
 * Class JsonResponse
 *
 * This class extends the Symfony JsonResponse class and provides a convenient
 * way to return a JSON response given an Annotated Response.
 *
 * If you would like to use this class you will need the dependency:
 * composer require symfony/http-foundation
 *
 * @code
 * $response = new \AKlump\AnnotatedResponse\HttpFoundation\JsonResponse(
 *   \AKlump\AnnotatedResponse\AnnotatedResponse::create()
 *     ->setResult('created')
 *     ->setHttpStatus(201)
 * );
 * @endcode
 */
class JsonResponse extends SymfonyJsonResponse {

  /**
   * Return this as a Symfony JsonResponse instance.
   */
  public function __construct(AnnotatedResponseInterface $response, array $headers = []) {
    parent::__construct($response->jsonSerialize(), $response->getHttpStatus(), $headers);
  }

}
