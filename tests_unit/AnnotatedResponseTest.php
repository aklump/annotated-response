<?php

namespace AKlump\AnnotatedResponse\Tests\Unit;

use AKlump\AnnotatedResponse\AnnotatedResponse;
use AKlump\AnnotatedResponse\AnnotatedResponseInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

/**
 * @covers \AKlump\AnnotatedResponse\AnnotatedResponse
 * @uses   \AKlump\AnnotatedResponse\Result\GetResultByCodeDefaultEnglish
 */
class AnnotatedResponseTest extends TestCase {

  public function testSetTooLongResultThrows() {
    $this->expectException(\InvalidArgumentException::class);
    $result = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed.';
    AnnotatedResponse::create()->setResult($result);
  }

  public function dataFortestAutomaticResultByCodeProvider() {
    $tests = [];
    $tests[] = [
      201,
      'created',
    ];
    $tests[] = [
      303,
      'succeeded',
    ];
    $tests[] = [
      404,
      'failed',
    ];

    return $tests;
  }

  /**
   * @dataProvider dataFortestAutomaticResultByCodeProvider
   */
  public function testAutomaticResultByCode(int $code, string $expected_result) {
    $response = (new AnnotatedResponse())->setHttpStatus($code);
    $this->assertSame($expected_result, $response->jsonSerialize()['result']);
  }

  public function testJsonSerialize() {
    $response = new AnnotatedResponse();
    $response
      ->setHttpStatus(200)
      ->setResult('Success')
      ->setMessage('Login complete.')
      ->setData(['lorem' => 'L'])
      ->addUserMessage(LogLevel::INFO, 'You have been logged in.')
      ->addUserMessage(LogLevel::NOTICE, "You've got mail!", ['count' => 3]);
    $json = json_encode($response);
    $data = json_decode($json, TRUE);

    $this->assertSame(['lorem' => 'L'], $data['data']);
    $this->assertSame('Login complete.', $data['message']);
    $this->assertSame('Success', $data['result']);

    $this->assertSame([], $data['user_messages'][0]['context']);
    $this->assertSame('info', $data['user_messages'][0]['level']);
    $this->assertSame('You have been logged in.', $data['user_messages'][0]['message']);

    $this->assertSame(['count' => 3], $data['user_messages'][1]['context']);
    $this->assertSame('notice', $data['user_messages'][1]['level']);
    $this->assertSame("You've got mail!", $data['user_messages'][1]['message']);
  }

  public function dataFortestCreateFromExceptionCodeIsExpectedProvider() {
    $tests = [];
    $tests[] = [
      new \Exception('Foo is not bar'),
      500,
    ];
    $tests[] = [
      new \RuntimeException('Access denied', 403),
      403,
    ];

    return $tests;
  }

  /**
   * @dataProvider dataFortestCreateFromExceptionCodeIsExpectedProvider
   */
  public function testCreateFromExceptionHttpStatusIsExpected(\Exception $exception, int $expected_code) {
    $response = AnnotatedResponse::createFromException($exception);
    $this->assertInstanceOf(AnnotatedResponseInterface::class, $response);
    $this->assertSame($expected_code, $response->getHttpStatus());
  }

  public function testCreateMethod() {
    $response = AnnotatedResponse::create();
    $this->assertInstanceOf(AnnotatedResponseInterface::class, $response);
  }
}
