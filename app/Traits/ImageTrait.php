<?php

namespace App\Traits;

use App\Models\FoodRestaurant;
use App\Models\Images;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ImageTrait
{
    /* Declaring constant variable */
    protected $cleanInput;
    protected $result;
    protected $imageService;

    public function initializeImageTrait()
    {
        $this->imageService = app(ImageService::class);
    }

    public function createImage(
        Request $request,
        int $linkId,
        int $genre,
        int $id,
        string $imgDir
    ): Model {
        $image_data = [];
        $image_data['link_id'] = $linkId;
        $image_data['genre'] = $genre;
        $images[] = $request->file('upload_url');

        foreach ($images as $image) {
            $image_data['upload_url'] = $image;
            // dd($image_data);
            if (!empty($id)) {
                $result = $this->imageService->update($image_data, $id, $imgDir, 'public');
            } else {
                $result = $this->imageService->store($image_data, $imgDir, 'public');
            }
        }
        return $result;
    }

    public function createImageTest(Model $model, array $images, string $imageDir, string $genre  )
    {
        // dd($model);
        // dd($images);
        // dd($imageDir);

        $imageDatas = [];
        foreach ($images as $image) {
            if (is_array($image)) {
                // If images are nested, loop again
                foreach ($image as $img) {
                    if ($img instanceof \Illuminate\Http\UploadedFile) {
                        // dd($img); // Check if the file is now detected
                        // Generate the image name and save the file
                        //dd($imageDir);
                        $this->imageService->setImageDirectory($imageDir, 'public');
                        $finalImagePath = $this->imageService->SavePhysicalImage($img);

                         //dd($finalImagePath);
                        // Prepare the data to be saved
                        // $imageDatas[] = ['url' => $finalImagePath];
                        // Prepare the data to be saved
                        $imageDatas[] = [
                            'upload_url' => $finalImagePath,
                            'gener' => $genre // Include genre if provided
                        ];

                    }
                }
            } elseif ($image instanceof \Illuminate\Http\UploadedFile) {
                // dd($image); // Check if this now works
            }
        }
        $existingImage = $model->images()->first();
        if ($existingImage) {
            // dd($imageDatas[0]);
            return $model->images()->update($imageDatas[0]);
        }


        return $model->images()->createMany($imageDatas);
    }




    public function deleteImage($imageId)
    {
        // dd($imageId);
        $image = Images::find($imageId);
        // dd($image);
        if (!$image) {
            return 'unsuccess';
        }
        Storage::delete($image->upload_url);
        return $image->delete() ? 'success' : 'unsuccess';
    }



        public function updateImageTest(Model $model, array $images, string $imageDir, string $genre = 'food')
{
    // Initialize image data array
    $imageDatas = [];

        foreach ($images as $image) {
        if (is_array($image)) {
            // Loop through nested images
            foreach ($image as $img) {
                if ($img instanceof \Illuminate\Http\UploadedFile) {
                    $this->imageService->setImageDirectory($imageDir, 'public');
                    $finalImagePath = $this->imageService->SavePhysicalImage($img);

                        // Prepare image data
                    $imageDatas[] = [
                        'upload_url' => $finalImagePath,
                        'gener' => $genre
                    ];
                }
            }
        } elseif ($image instanceof \Illuminate\Http\UploadedFile) {
            $this->imageService->setImageDirectory($imageDir, 'public');
            $finalImagePath = $this->imageService->SavePhysicalImage($image);

                // Prepare image data
            $imageDatas[] = [
                'upload_url' => $finalImagePath,
                'gener' => $genre
            ];
        }
    }

        // Fetch existing image
    $existingImage = $model->images()->first();

        if ($existingImage && !empty($imageDatas)) {
        // Delete old image from storage
        Storage::delete($existingImage->upload_url);

            // Update existing image record
        return $existingImage->update($imageDatas[0]);
    } elseif (!empty($imageDatas)) {
        // No existing image, create new entry
        return $model->images()->createMany($imageDatas);
    }

        return false;
}





// public function updateImageTest(Model $model, array $images, string $imageDir, string $genre = 'food')
// {
//     // Initialize image data array
//     $imageDatas = [];

//     foreach ($images as $image) {
//         if (is_array($image)) {
//             // Loop through nested images
//             foreach ($image as $img) {
//                 if ($img instanceof \Illuminate\Http\UploadedFile) {
//                     $this->imageService->setImageDirectory($imageDir, 'public');
//                     $finalImagePath = $this->imageService->SavePhysicalImage($img);

//                     // Prepare image data
//                     $imageDatas[] = [
//                         'upload_url' => $finalImagePath,
//                         'gener' => $genre
//                     ];
//                 }
//             }
//         } elseif ($image instanceof \Illuminate\Http\UploadedFile) {
//             $this->imageService->setImageDirectory($imageDir, 'public');
//             $finalImagePath = $this->imageService->SavePhysicalImage($image);

//             // Prepare image data
//             $imageDatas[] = [
//                 'upload_url' => $finalImagePath,
//                 'gener' => $genre
//             ];
//         }
//     }

//     // Fetch existing image
//     $existingImage = $model->images()->first();
//         dd($existingImage);

//     if ($existingImage && !empty($imageDatas)) {
//         // Delete old image from storage
//         $oldImagePath = $existingImage->upload_url;  // This should be something like 'food/image.jpg'
//             // dd($oldImagePath);

//         // Construct the full path for deletion
//         $fullImagePath = 'public/' . $oldImagePath;  // This should be 'public/food/image.jpg'

//         // Check if the file exists in storage and delete it
//         if (Storage::disk('public')->exists($fullImagePath)) {
//             Storage::disk('public')->delete($fullImagePath);
//         }

//         // Update existing image record
//         return $existingImage->update($imageDatas[0]);
//     } elseif (!empty($imageDatas)) {
//         // No existing image, create new entry
//         return $model->images()->createMany($imageDatas);
//     }

//     return false;
// }

}

// namespace App\Traits;

// use App\Contracts\LocationInterface;
// use App\DB\Core\Crud;
// use App\Models\Images;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Facades\Storage;

// trait ImageTrait
// {
//



//     public function updateImage($request, $oldImageDatas,  $linkId, $gener, $interface, $folderName, $tableName): bool
//     {
//         if ($request->hasFile('upload_url')) {
//             foreach ($oldImageDatas as $record) {
//                 if (Storage::exists($record->upload_url)) {
//                     Storage::delete($record->upload_url);
//                 }
//                 $interface->delete('Images', $record->id);
//             }
//             return $this->storeImage($request, $linkId, $gener, $interface, $folderName, $tableName,$imageableType);
//         }
//         return false;
//     }

//     public function deleteImage($interface, $oldImageDatas)
//     {
//         foreach ($oldImageDatas as $record) {
//             if (Storage::exists($record->upload_url)) {
//                 Storage::delete($record->upload_url);
//             }
//             $interface->delete('Images', $record->id);
//         }
//     }
// }
