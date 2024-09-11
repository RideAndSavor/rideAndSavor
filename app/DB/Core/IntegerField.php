<?php

namespace App\DB\Core;

use App\Exceptions\CrudException;

class IntegerField extends Field
{
  public function execute()
  {
    return $this->value;
  }
}
