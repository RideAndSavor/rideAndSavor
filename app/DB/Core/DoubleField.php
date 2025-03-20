<?php

namespace App\DB\Core;

use App\Exceptions\CrudException;

class DoubleField extends Field
{
  public function execute()
  {
    return $this->value;
  }
}
