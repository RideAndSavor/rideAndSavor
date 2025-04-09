<?php

namespace App\Services;

use illuminate\Database\Eloquent\Model;
use App\Contracts\ImageInterface;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    protected $imageRepository;
    protected $record;
    protected $productRepository;
    public static string $imageDirectory = '';
    public static string $diskName = '';


    public function __construct(ImageInterface $imageInterface)
    {
        $this->imageRepository = $imageInterface;
    }

    public function store(array $data, string $imageDir = null, string $diskName = null)
    {
        self::setImageDirectory($imageDir, $diskName);
        return $this->imageRepository->store($data, $imageDir, $diskName);
    }

    public function update(array $data, int $id, string $imageDir = null, string $diskName = null)
    {
        self::setImageDirectory($imageDir, $diskName);
        $this->checkRecordExists($id);
        return $this->imageRepository->update($data, $id, $imageDir, $diskName);
    }

    public function delete(int $id)
    {
        $this->checkRecordExists($id);
        return $this->imageRepository->delete($id);
    }

    public static function setImageDirectory($imgDir, $diskName)
    {
        //dd($imgDir);
        // dd($diskName);
        self::$diskName = $diskName;
        self::$imageDirectory = $imgDir;
    }

    public static function SavePhysicalImage($uploadedFile)
    {
        // dd($uploadedFile);
        $imageName = round(microtime(true) * 1000)  . '.' . $uploadedFile->extension();
        // dd($imageName);
        $finalImagePath = self::$imageDirectory . $imageName;
        $uploadedFile->storeAs(self::$imageDirectory, $imageName, self::$diskName);
        return $finalImagePath;
    }

    public function checkRecordExists($id)
    {
        $this->record = $this->imageRepository->getById($id);


        if (!$this->record) {
            throw CustomException::notFound();
        }
        /* Delete physical image from path */
        $this->deleteImage($this->record->upload_url);
        return $this->record;
    }

    public function deleteImage($uploadUrlPath): bool
    {
        return $uploadUrlPath ? Storage::disk(self::$diskName)->delete($uploadUrlPath) : false;
    }

    public function updateImage(Model $parentModel, int $imageId, \Illuminate\Http\UploadedFile $newImage, string $imageDir, string $diskName)
    {
        self::setImageDirectory($imageDir, $diskName);
        $this->checkRecordExists($imageId);
        $finalImagePath = $this->SavePhysicalImage($newImage);
        return $this->imageRepository->updateImage($parentModel, $imageId, $finalImagePath);
    }


    public function morphImageSave(Model $parentModel, array $images, string $imageDir)
    {
        $imageDatas = [];

        foreach ($images as $image) {
            if ($image instanceof \Illuminate\Http\UploadedFile) {
                // Generate the image name and save the file
                $this->setImageDirectory($imageDir, 'public');
                $finalImagePath = $this->SavePhysicalImage($image);
                // Prepare the data to be saved
                $imageDatas[] = ['upload_url' => $finalImagePath];
            }
        }
        // $existingImage = $this->productRepository->getDataWithRelation("Image")->first();
        // if ($existingImage) {
        //   //return $model->images()->update($imageDatas[0]);
        // }

        return $this->imageRepository->MorphStore($parentModel, $imageDatas, "images");
    }


    // public function storeImage($uploadUrlPath): bool
    // {
    //     $path = Storage::disk(self::$diskName)->putFile('profiles', $file);
    //     return $uploadUrlPath ? Storage::disk(self::$diskName)->delete($uploadUrlPath) : false;
    // }
}
