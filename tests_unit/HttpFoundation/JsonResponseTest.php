<?php

namespace AKlump\AnnotatedResponse\Tests\Unit\HttpFoundation;

use AKlump\AnnotatedResponse\AnnotatedResponse;
use AKlump\AnnotatedResponse\HttpFoundation\JsonResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AKlump\AnnotatedResponse\HttpFoundation\JsonResponse
 * @uses \AKlump\AnnotatedResponse\AnnotatedResponse
 */
class JsonResponseTest extends TestCase {

  public function testConstructor() {
    $response = new JsonResponse(
      AnnotatedResponse::create()
        ->setResult('created')
        ->setHttpStatus(201)
    );
    $this->assertSame(201, $response->getStatusCode());
    $this->assertSame('{"result":"created","message":"","user_messages":[],"data":[]}', $response->getContent());
  }
}
