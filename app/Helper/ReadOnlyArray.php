<?php

namespace App\Helper;

use App\Exceptions\CrudException;
use ArrayObject;

class ReadOnlyArray extends ArrayObject
{
    public function offsetSet(mixed $key, mixed $value): void
    {
        // dd('ok');
        throw CrudException::readOnlyArray();
    }

    public function offsetUnset(mixed $key): void
    {
        // dd('ok');
        throw CrudException::readOnlyArray();
    }
}
