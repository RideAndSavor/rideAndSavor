<?php

namespace App\Traits;

use App\DB\Core\Crud;
use App\Models\Images;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

trait ImageTrait
{
    public function storeImage($request, $linkId, $gener, $interface, $folder_name, $tableName): bool
    {
        if ($request->hasFile('upload_url')) {
            $image_data = [];
            $image_data['link_id'] = $linkId;
            $image_data['gener'] = $gener;
            $images = $request->file('upload_url');
            $imageCount = is_array($images) ? count($images) : 1;

            if ($imageCount == 1) {
                $image_data['upload_url'] = $request->file('upload_url');
                return $interface->store('Images', $image_data, $folder_name, $tableName) ? true : false;
            }

            foreach ($images as $image) {
                $image_data['upload_url'] = $image;
                $interface->store('Images', $image_data, $folder_name, $tableName);
            }
            return true;
        }
        return false;
    }

    public function updateImage($request, $oldImageDatas,  $linkId, $gener, $interface, $folderName, $tableName): bool
    {
        if ($request->hasFile('upload_url')) {
            foreach ($oldImageDatas as $record) {
                if (Storage::exists($record->upload_url)) {
                    Storage::delete($record->upload_url);
                }
                $interface->delete('Images', $record->id);
            }
            return $this->storeImage($request, $linkId, $gener, $interface, $folderName, $tableName);
        }
        return false;
    }

    public function deleteImage($interface, $oldImageDatas)
    {
        foreach ($oldImageDatas as $record) {
            if (Storage::exists($record->upload_url)) {
                Storage::delete($record->upload_url);
            }
            $interface->delete('Images', $record->id);
        }
    }
}
