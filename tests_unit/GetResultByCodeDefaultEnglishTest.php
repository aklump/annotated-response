<?php

/**
 * @covers \AKlump\AnnotatedResponse\Result\GetResultByCodeDefaultEnglish
 */
class GetResultByCodeDefaultEnglishTest extends \PHPUnit\Framework\TestCase {

  public function dataFortestInvokeProvider() {
    $tests = [];
    $tests[] = [
      200,
      'succeeded',
    ];
    $tests[] = [
      300,
      'succeeded',
    ];
    $tests[] = [
      201,
      'created',
    ];
    $tests[] = [
      400,
      'failed',
    ];

    return $tests;
  }

  /**
   * @dataProvider dataFortestInvokeProvider
   */
  public function testInvoke(int $code, string $expected) {
    $this->assertSame($expected, (new \AKlump\AnnotatedResponse\Result\GetResultByCodeDefaultEnglish())($code));
  }
}
