<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Configuration\Exceptions;

class CustomException extends Exception
{

  //information response
  public static function oK(): static
  {
    return new static('the request has succeeded!', 200);
  }

  public static function created(): static
  {
    return new static("The record has been created!", 201);
  }

  public static function accepted(): static
  {
    return new static("The record has been accepted!", 202);
  }

  public static function noContent(): static
  {
    return new static("No content!", 204);
  }

  public static function alreadyReported(): static
  {
    return new static("Already reported!", 208);
  }

  //Client Error Responses
  public static function badRequest(): static
  {
    return new static("Bad Request!", 400);
  }

  public static function unauthorized(): static
  {
    return new static("You are unauthorized!", 401);
  }

  public static function paymentRequired(): static
  {
    return new static("Payment requires to perform this action!", 402);
  }

  public static function forbidden(): static
  {
    return new static("Forbidden!", 403);
  }

  public static function notFound(): static
  {
    return new static("The record not found!", 404);
  }

  public static function methodNotAllowed(): static
  {
    return new static("You cannot call this method!", 405);
  }

  public static function notAcceptable(): static
  {
    return new static("Doesn't conforms to the criteria given by the user agent!", 406);
  }

  public static function requestTimeout(): static
  {
    return new static("Request timeout!", 408);
  }

  //Server error response

  public static function internalServerError(): static
  {
    return new static("Internal server error occur!", 500);
  }

  public static function serviceUnavailable(): static
  {
    return new static("The service is not available!", 503);
  }

  public static function versionNotSupported(): static
  {
    return new static("The version is not supported currently!", 505);
  }

  //Eloquent exceptions
  public static function relationNotFoundException(): static
    {
        return new static("Relation not found exception", 404);
    }

    public static function BadMethodCallException(): static
    {
        return new static("Calling undefined method", 400);
    }
}
