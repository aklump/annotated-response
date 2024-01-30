<?php

namespace AKlump\AnnotatedResponse\Result;

/**
 * Interface ResultByCodeInterface
 *
 * This interface represents a class that converts HTTP status code to a result
 * word or phrase.
 *
 * @see \AKlump\AnnotatedResponse\AnnotatedResponse::setHttpStatus
 */
interface ResultByCodeInterface {

  public function __invoke(int $http_status_code): string;
}
