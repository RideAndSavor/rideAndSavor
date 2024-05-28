<?php

namespace App\Db\Core;

use App\Exceptions\CrudException;
use App\Exceptions\CustomException;
use Exception;
use illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class Crud
{
    public function __construct(
        private Model $model,
        private ?array $data,
        private ?int $id,
        private $editMode,
        private $deleteMode,
    ) {
        // dd($data);
        // $this->model = $model;
        // $this->data = $data;
        // $this->id = $id;
        // $this->editMode = $editMode;
        // $this->deleteMode = $deleteMode;
        self::$tableName = $model->getTable();
    }

    public static string $imageDirectory = '';
    public static string $tableName = '';
    private ?Model $record = null;

    public function setImageDirectory(string $directoryPath, string $tablename)
    {
        self::$imageDirectory = $directoryPath;
        self::$tableName = $tablename;
    }

    public function getData(string $model, string $id)
    {
        $modelInstance = new $model;
        return $modelInstance->findOrFail($id);
    }

    public function execute(): mixed
    {
        try {
            if ($this->editMode) {
                return $this->handleEditMode();
            } elseif ($this->deleteMode) {
                return $this->handleDeleteMode();
            } else {
                return $this->handleStoreMode();
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    protected function iterateData(array $data, ?Model $record = null): Model
    {
        $target = $record ?? $this->model;
        foreach ($data as $column => $value) {
            // var_dump($column,$value);
            $target->{$column} = $this->savableField($column)->setValue($value)->execute();

        }
        if (!$target) {
            throw CrudException::prepareDataFormat();
        }
        return $target;
    }

    protected function handleStoreMode(): Model|bool
    {
        $model = $this->iterateData($this->data, null);
        $model = $model->save() ? $this->model : false;
        if (!$model) {
            throw CrudException::internalServerError();
        }
        return $model;
    }

    protected function handleEditMode(): Model|bool
    {
        $this->record = $this->model->find($this->id);
        if (!$this->record) {
            throw CustomException::notFound();
        }
        if ($this->model->getTable() === Config::get('variables.IMAGE')) {
            $this->deleteImage();
        }

        $record = $this->iterateData($this->data, $this->record);
        $record = $record->save() ? $this->record : false;
        if (!$record) {
            throw CrudException::internalServerError();
        }
        return $record;
    }

    protected function handleDeleteMode(): bool
    {
        $this->record = $this->model->find($this->id);
        if (!$this->record) {
            throw CustomException::notFound();
        }
        $success = $this->record->delete() ? true : false;
        if (!$success) {
            throw CustomException::internalServerError();
        }
        return $success;
    }

    public function savableField($column): object
    {
        return $this->model->saveableFields($column);
    }

    public function deleteImage(): bool
    {
        $old_image = $this->record->upload_url;
        return Storage::delete($old_image);
    }

    public static function storeImage($value, $imageDirectory, $imageName)
    {
        $value->storeAs($imageDirectory, $imageName);
    }
}
