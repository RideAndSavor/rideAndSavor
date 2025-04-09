<?php

namespace App\Traits;

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
        $imageDatas = [];
        foreach ($images as $image) {
            if (is_array($image)) {
                foreach ($image as $img) {
                    if ($img instanceof \Illuminate\Http\UploadedFile) {
                        $this->imageService->setImageDirectory($imageDir, 'public');
                        $finalImagePath = $this->imageService->SavePhysicalImage($img);

                        $imageDatas[] = [
                            'upload_url' => $finalImagePath,
                            'gener' => $genre
                        ];

                    }
                }
            } elseif ($image instanceof \Illuminate\Http\UploadedFile) {
            }
        }
        $existingImage = $model->images()->first();
        if ($existingImage) {
            return $model->images()->update($imageDatas[0]);
        }
        return $model->images()->createMany($imageDatas);
    }


    public function updateImageTest(Model $model, array $images, array $deleteImageIds, string $imageDir, string $genre)
    {
        $existingImages = $model->images()->get();
        $imageDatas = [];

        if (!empty($deleteImageIds)) {
            $model->images()->whereIn('id', $deleteImageIds)->delete();
            // Optionally delete physical images
            // foreach ($deleteImageIds as $id) {
            //     $image = $model->images()->find($id);
            //     if ($image) {
            //         $this->imageService->deletePhysicalImage($image->upload_url);
            //     }
            // }
        }
        foreach ($images as $image) {
            if ($image instanceof \Illuminate\Http\UploadedFile) {
                $this->imageService->setImageDirectory($imageDir, 'public');
                $finalImagePath = $this->imageService->SavePhysicalImage($image);

                $imageDatas[] = [
                    'upload_url' => $finalImagePath,
                    'gener' => $genre
                ];
            }
        }
            if (!empty($imageDatas)) {
                $model->images()->createMany($imageDatas);
            }
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
}
