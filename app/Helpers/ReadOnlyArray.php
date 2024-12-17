<?php

namespace App\Helper;

use App\Exceptions\CrudException;
use ArrayObject;

class ReadOnlyArray extends ArrayObject
{
    public function offsetSet(mixed $key, mixed $value): void
    {
        throw CrudException::readOnlyArray();
    }

    public function offsetUnset(mixed $key): void
    {
        throw CrudException::readOnlyArray();
    }
}
