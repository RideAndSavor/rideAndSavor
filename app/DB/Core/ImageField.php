<?php

namespace App\DB\Core;

use App\DB\Core\Crud;
use App\Exceptions\CrudException;
use Illuminate\Support\Facades\Config;

class ImageField extends Field
{
  public $tableName, $imageDirectory;

  public function __construct()
  {
    $this->tableName = Crud::$tableName;
    $this->imageDirectory = Crud::$imageDirectory;
  }

  public function execute()
  {

    if (!$this->value) {
      throw CrudException::emptyData();
    }

    if ($this->tableName === Config::get('variable.IMAGES_TABLE')) {
      $uploadedFile = $this->value;
    //   dd($uploadedFile);
      $imageName = round(microtime(true) * 1000)  . '.' . $uploadedFile->extension();
    //   dd($imageName);
      $finalImagePath = $this->imageDirectory . $imageName;
    //   dd($finalImagePath);
      Crud::storeImage($uploadedFile, $this->imageDirectory, $imageName);
    //   $imagePath=Crud::storeImage($uploadedFile, $this->imageDirectory, $imageName);
    //   dd($imagePath);
      $this->value = $finalImagePath;
      return $this->value;
    //   dd($this->value);
    }
  }
}
