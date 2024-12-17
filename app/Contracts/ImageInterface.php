<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Contracts\BaseInterface;

interface ImageInterface extends BaseInterface
{
    public function getByImageId($imageId);
    public function updateImage(Model $parentModel, int $imageId, string $newImage);
}
