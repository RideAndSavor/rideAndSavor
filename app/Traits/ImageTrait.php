<?php

namespace App\Traits;
use App\Db\Core\Crud;
use Illuminate\Database\Eloquent\Model;

trait ImageTrait
{

    public function storeImage($request, $linkId, $gener, $interface,$folder_name,$tableName): bool
    {
        if ($request->hasFile('upload_url')) {
            $image_data = [];
            $image_data['link_id'] = $linkId;
            $image_data['gener'] = $gener;
            $images = $request->file('upload_url');
            $imageCount = is_array($images) ? count($images) : 1;

            if($imageCount == 1) {
                $image_data['upload_url'] = $request->file('upload_url');
                return $interface->store('Images', $image_data,$folder_name,$tableName) ? true : false;
            }

            foreach ($images as $image) {
                $image_data['upload_url'] = $image;
                $interface->store('Images', $image_data,$folder_name,$tableName);
            }
            return true;
        }
    }

    // public function updateImage($request, $linkId, $genre, $interface,$folder_name,$tableName,$id): bool
    // {
    //     if ($request->hasFile('upload_url')) {
    //         $image_data = [
    //             'link_id'=>$linkId,
    //             'genre'=>$genre
    //         ];
    //         $images = $request->file('upload_url');
    //         $imageCount = is_array($images) ? count($images) : 1;

    //         if ($imageCount == 1) {
    //             $image_data['upload_url'] = $request->file('upload_url');
    //             return $interface->update('Images', $image_data, $id);
    //         }
    //         foreach ($images as $image) {
    //             $image_data['upload_url'] = $image;
    //             $interface->update('Images', $image_data, $id);
    //         }
    //     }
    // }

    public function updateImage($request, $linkId, $genre, $interface, $folder_name, $tableName, $id): bool
{
    if ($request->hasFile('upload_url')) {
        $image_data = [
            'link_id' => $linkId,
            'genre' => $genre,
        ];
        $images = $request->file('upload_url');
        $imageCount = is_array($images) ? count($images) : 1;

        $interface->deleteExistingImages($linkId, $tableName); // Assuming such a method exists.

        if ($imageCount == 1) {
            $image_data['upload_url'] =  $images->store($folder_name, 'public');
            return $interface->update('Images', $image_data, $id) ? true : false;
        }

        foreach ($images as $image) {
            $image_data['upload_url'] = $image->store($folder_name, 'public');
            $interface->update('Images', $image_data, $id);
        }
        return true;
    }
    return false;
}

}
