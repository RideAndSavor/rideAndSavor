<?php

namespace App\Db\Core;

use App\Exceptions\CrudException;

class IntegerField extends Field
{
  public function execute()
  {
    return $this->value;
  }
}
