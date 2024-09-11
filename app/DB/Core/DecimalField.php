<?php

namespace App\DB\Core;

use App\Exceptions\CrudException;

class DecimalField extends Field
{
  public function execute()
  {
    return $this->value;
  }
}
