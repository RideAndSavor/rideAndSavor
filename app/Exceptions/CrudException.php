<?php

namespace App\Exceptions;

class CrudException extends CustomException
{

  public static function missingAttributeException(): self
  {
    return new self('Key and value does not match!', 404);
  }

  public static function prepareDataFormat(): self
  {
    return new self('Prepare data format is something!', 500);
  }

  public static function argumentCountError(): self
  {
    return new self('Too few arguments to function!', 500);
  }

  public static function emptyData(): self
  {
    return new self('The data should not be null!', 404);
  }

}