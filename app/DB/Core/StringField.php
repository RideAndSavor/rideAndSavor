<?php

namespace App\DB\Core;

use App\Exceptions\CrudException;

class StringField extends Field
{
    public function execute()
    {
        return $this->value;
    }
}
