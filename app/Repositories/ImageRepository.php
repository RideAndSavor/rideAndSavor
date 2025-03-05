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



// class ImageRepository extends BaseRepository implements ImageInterface
// {
//     public function __construct()
//     {
//         parent::__construct(class_basename("Images"));
//     }
//     public function getByImageId($imageId)
//     {
//         return [];
//     }

//     public function updateImage(Model $parentModel, int $imageId, string $newImage)
//     {
//         $image = $parentModel->images()->findOrFail($imageId);
//         $image->update(['upload_url' => $newImage]);
//         return $image;
//     }
//     // public function storeImages(string $modelName, array $data, string $folder_name, string $tableName)
//     // {
//     //     // Assuming you're using the Eloquent model to interact with the database
//     //     $image = new Images(); // Assuming you have an Image model
//     //     $image->link_id = $data['link_id'];
//     //     $image->gender = $data['gender'];

//     //     // Store the image file in the specified folder
//     //     $imagePath = $data['upload_url']->store($folder_name, 'public'); // Store in the 'storage/app/public' directory

//     //     // Save the image path to the database
//     //     $image->upload_url = $imagePath;

//     //     // Optionally, if you want to save more data to the related table, you can use $tableName

//     //     return $image->save();
//     // }

// }
