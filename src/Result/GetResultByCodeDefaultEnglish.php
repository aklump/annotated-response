<?php

namespace AKlump\AnnotatedResponse\Result;

/**
 * Class GetResultByCodeDefaultEnglish
 *
 * Responsible for getting the English result based on the provided status code.
 *
 * @see \AKlump\AnnotatedResponse\AnnotatedResponse::__construct
 */
class GetResultByCodeDefaultEnglish implements ResultByCodeInterface {

  public function __invoke(int $code): string {
    $category_number = (int) substr($code, 0, 1);
    if ($category_number >= 4) {
      return 'failed';
    }
    if ($code === 201) {
      return 'created';
    }

    return 'succeeded';
  }

}
