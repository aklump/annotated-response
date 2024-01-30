# Annotated Response

![Hero](images/hero.jpg)

This library provides a response model for API design. It features:

* ... data transport in every response.
* ... a single system result phrase and message.
* ... [PSR logger style](https://www.php-fig.org/psr/psr-3) user messages with every response.
* ... optional integration with the [Symfony HttpFoundation Component](https://symfony.com/doc/current/components/http_foundation.html)

## Example Response Body

```json
{
    "data": {
        "user": "foobar"
    },
    "message": "Login complete.",
    "result": "Success",
    "user_messages": [
        {
            "context": [],
            "level": "info",
            "message": "You have been logged in."
        },
        {
            "context": {
                "count": 3
            },
            "level": "notice",
            "message": "You've got mail!"
        }
    ]
}
```

## PHP Code to Generate the Example

```php
<?php
$response = new \AKlump\AnnotatedResponse\AnnotatedResponse();
$response
  ->setHttpStatus(200)
  ->setData(['user' => 'foobar'])
  ->setMessage('Login complete.')
  ->addUserMessage(\Psr\Log\LogLevel::INFO, 'You have been logged in.')
  ->addUserMessage(\Psr\Log\LogLevel::NOTICE, "You've got mail!", ['count' => 3]);
```

## Symfony JsonResponse

Convert the above object to a JsonResponse object:

```php
return new \AKlump\AnnotatedResponse\HttpFoundation\JsonResponse($response);
```

## Install with Composer

1. Because this is an unpublished package, you must define it's repository in your project's _composer.json_ file. Add the following to _composer.json_:

    ```json
    "repositories": [
        {
            "type": "github",
            "url": "https://github.com/aklump/annotated-response"
        }
    ]
    ```

1. Then `composer require aklump/annotated-response:@dev`
