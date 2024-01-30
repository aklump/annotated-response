<?php

namespace AKlump\AnnotatedResponse;

use AKlump\AnnotatedResponse\Result\GetResultByCodeDefaultEnglish;
use AKlump\AnnotatedResponse\Result\ResultByCodeInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides an annotated structure for REST responses.
 *
 * @code
 *   return AnnotatedResponse::create()
 *     ->setHttpStatus(406)
 *     ->setMessage("Event can't be loaded.")
 *     ->asJson();
 * @endcode
 *
 * @code
 *   try {
 *     // Perform something that might throw...
 *   }
 *   catch (\Exception $exception) {
 *     return AnnotatedResponse::createFromException($exception)->asJson();
 *   }
 * @endcode
 */
final class AnnotatedResponse implements AnnotatedResponseInterface {

  /**
   * @var array
   */
  private $responseBody;

  /**
   * @var int
   */
  private $statusCode;

  /**
   * @var \AKlump\AnnotatedResponse\Result\ResultByCodeInterface
   */
  private $resultByCode;

  /**
   * Constructor for Response class.
   *
   * Initializes a new instance of the Response class with optional
   * ResultByCodeInterface parameter. If the ResultByCodeInterface parameter is
   * not provided, it defaults to an instance of GetResultByCodeDefaultEnglish.
   * The initial status code is set to 200, and the initial response body
   * contains empty values for 'result', 'message', 'user_messages', and 'data'.
   *
   * @param ResultByCodeInterface|null $result_by_code
   *   (optional) An instance of ResultByCodeInterface. Defaults to
   *   GetResultByCodeDefaultEnglish if not provided.
   */
  public function __construct(ResultByCodeInterface $result_by_code = NULL) {
    $this->setResultByCode($result_by_code ?? new GetResultByCodeDefaultEnglish());
    $this->statusCode = 200;
    $this->responseBody = [
      'result' => '',
      'message' => '',
      'user_messages' => [],
      'data' => [],
    ];
  }

  /**
   * Set the result by code handler.
   *
   * This method sets the result by code object, which controls the result that
   * is automatically set when the status is first set.
   *
   * @param \AKlump\AnnotatedResponse\Result\ResultByCodeInterface $resultByCode
   *   The result by code object to be set.
   *
   * @return self
   *   The updated response instance.
   */
  public function setResultByCode(ResultByCodeInterface $resultByCode): self {
    $this->resultByCode = $resultByCode;

    return $this;
  }

  /**
   * Factory method to create a new instance.
   *
   * @return \AKlump\AnnotatedResponse\AnnotatedResponseInterface static
   *   A new response instance.
   */
  public static function create(): AnnotatedResponseInterface {
    return new self();
  }

  /**
   * Factory to create a new instance from an exception.
   *
   * If the exception code is in the HTTP response status code range, it will be
   * used.  It outside of this range it will be ignored.
   *
   * @param \Exception $exception
   *   The exception instance.
   *
   * @return static
   *   A new response instance.
   */
  public static function createFromException(Exception $exception) {
    $response = new self();
    $response->setHttpStatus(500);
    $code = $exception->getCode();
    // @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
    if ($code >= 100 && $code < 600) {
      $response->setHttpStatus($code);
    }

    return $response->setMessage($exception->getMessage());
  }

  /**
   * Set a result word or phrase.
   *
   * @param string $result
   *   A word or phrase to finished this sentence "The request has ____", e.g.
   *   "succeeded", "failed", "created", "deleted".  This will be set by the
   *   http status code when possible, unless explicitly set with this method.
   *
   * @return $this
   */
  public function setResult(string $result): AnnotatedResponseInterface {
    if (strlen($result) > 30) {
      throw new \InvalidArgumentException('The length may not exceed 30 characters');
    }
    $this->responseBody['result'] = $result;

    return $this;
  }


  /**
   * @param int $code
   *   The status code to be returned in the HTTP responses.
   *
   * @return $this
   *   Self for chaining.
   */
  public function setHttpStatus(int $code): AnnotatedResponseInterface {
    if (empty($this->responseBody['result'])) {
      $result = $this->resultByCode->__invoke($code);
      if ($result) {
        $this->setResult($result);
      }
    }
    $this->statusCode = $code;

    return $this;
  }

  public function getHttpStatus(): int {
    return $this->statusCode;
  }

  /**
   * Set a message to describe the result to the client.
   *
   * Compare this to AnnotatedResponse::addUserMessage()
   *
   * @param string $message
   *
   * @return $this
   */
  public function setMessage(string $message): AnnotatedResponseInterface {
    $this->responseBody['message'] = $message;

    return $this;
  }

  /**
   * Add a message appropriate to the end user.
   *
   * @param string $log_level
   *   One of \Psr\Log\LogLevel constants.
   * @param string $message
   * @param array $context
   *
   * @return $this
   */
  public function addUserMessage(string $log_level, string $message, array $context = []): AnnotatedResponseInterface {
    $this->responseBody['user_messages'][] = [
      'level' => $log_level,
      'message' => $message,
      'context' => $context,
    ];

    return $this;
  }

  /**
   * @param array $data
   *   Arbitrary data to send back in response.
   *
   * @return $this
   *   Self for chaining.
   */
  public function setData(array $data): AnnotatedResponseInterface {
    $this->responseBody['data'] = $data;

    return $this;
  }


  public function jsonSerialize() {
    return $this->responseBody;
  }
}
