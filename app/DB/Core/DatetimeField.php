<?php

namespace App\DB\Core;

use App\Exceptions\CrudException;
use Carbon\Carbon;

class DateTimeField extends Field
{
  public function execute()
  {
    if (!$this->value) {
      throw CrudException::emptyData();
    }
    return Carbon::parse($this->value)->toDateTimeString();
  }
}
