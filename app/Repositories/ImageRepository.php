<?php

namespace App\Repositories;

use App\Contracts\ImageInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class ImageRepository extends BaseRepository implements ImageInterface
{
    public function __construct()
    {
        parent::__construct(class_basename("Images"));
    }
    public function getByImageId($imageId)
    {
        return [];
    }

    public function updateImage(Model $parentModel, int $imageId, string $newImage)
    {
        $image = $parentModel->images()->findOrFail($imageId);
        $image->update(['upload_url' => $newImage]);
        return $image;
    }
}
