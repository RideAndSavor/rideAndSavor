<?php

namespace App\DB\Core;

use Exception;
use App\Models\Images;
use Illuminate\Http\Request;
use App\Exceptions\CrudException;
use Illuminate\Http\UploadedFile;
use App\Exceptions\CustomException;
use illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Crud
{
    public static ?string $tableName = null;
    public static ?string $imageDirectory = null;

    public function __construct(
        private Model $model,
        private ?array $data = null,
        private ?int $id = null,
        private ?string $relation = null,
        private bool $storeMode = false,
        private bool $twoModelsStoreMode = false,
        private bool $editMode = false,
        private bool $deleteMode = false,
        private ?Model $record = null,
    ) {
        // dd($this->model);
    }
    public function execute(): mixed
    {
        // dd($this->deleteMode);
        // dd($this->editMode);
        // dd($this->storeMode);
        try {
            if ($this->editMode) {
                return $this->handleEditMode();
            } elseif ($this->deleteMode) {
                return $this->handleDeleteMode();
            } elseif ($this->storeMode) {
                return $this->handleStoreMode();
            } else {
                return $this->handleTwoModelsStoreMode();
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    protected function iterateData(array $data, ?Model $record = null): Model
    {
        $target = $record ?? $this->model;
        // dd($target);
        // dd($data);

        foreach ($data as $column => $value) {
            // dd($data);
            // dd(is_object($value));
            // dd($column === 'upload_url');
            if (!is_object($value) || $column === 'upload_url') {
                // dd('ok');
                $target->{$column} = $this->savableField($column)->setValue($value)->execute();
                // dd($target->{$column} );
            } else {
                $target->$column = $value;
            }
        }
        return $target;
    }

    protected function handleStoreMode(): Model|bool
    {

        // dd($this->data);
        // dd($this->model);
        // Save the main model data
        $model = $this->iterateData($this->data, null);
        // dd($model);
        $model = $model->save() ? $this->model : false;
        // dd($model);

        if (!$model->wasRecentlyCreated) {
            throw CrudException::internalServerError();
        }

        return $model;
    }

    protected function handleTwoModelsStoreMode(): Model
    {
        $instance = $this->model->findOrFail($this->id);
        $relationName = $this->relation;
        if (!method_exists($instance, $relationName)) {
            throw CrudException::methodNotFound();
        }
        return tap($instance)->$relationName()->attach($this->data);
    }

    protected function handleEditMode(): Model|bool
    {
        // dd("OK");
        $this->record = $this->model->findOrFail($this->id);
        $record = $this->iterateData($this->data, $this->record);
        // dd($record);
        return $record->save() ? $this->record : false;
    }

    // : bool
    protected function handleDeleteMode()
    {
        $this->record = $this->model->findOrFail($this->id);
        $success = $this->record->delete() ? true : false;
        if (!$success) {
            throw CustomException::internalServerError();
        }
        return $success;
    }

    public function savableField($column): object
    {
        // dd($column);
        return $this->model->saveableFields($column);
    }

    public static function setImageDirectory(string $folderName, string $tableName)
    {
        // dd($tableName);
        self::$imageDirectory = $folderName; // Store folder name, e.g., "public/foods/"
        // dd(self::$imageDirectory);
        self::$tableName = $tableName; // Store table name, e.g., "images"
        //  dd(self::$tableName);
    }

    public static function storeImage(UploadedFile $file, string $directory, string $imageName): string
        {
            // dd($file);
            // dd($directory);
            // dd($imageName);
            try {
                // Ensure directory exists
                // dd(Storage::exists($directory));
                if (!Storage::exists($directory)) {
                    Storage::makeDirectory($directory);
                }

                // Store the file in the specified directory
                $path = Storage::putFileAs($directory, $file, $imageName);
                // dd($path);"public/foods//1740537078350.jpg" // app\DB\Core\Crud.php:141

                // Return the stored image path
                return $path;
            } catch (Exception $e) {
                throw new CrudException("Failed to store image: " . $e->getMessage());
            }
        }
}


