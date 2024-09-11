<?php

namespace App\Db\Core;

use App\Exceptions\CrudException;

class DecimalField extends Field
{
  public function execute()
  {
    return $this->value;
  }
}
