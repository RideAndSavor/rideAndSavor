<?php

namespace App\Db\Core;

use Exception;
use App\Services\ImageService;
use App\Exceptions\CrudException;
use App\Exceptions\CustomException;
use illuminate\Database\Eloquent\Model;


class Crud
{
    public function __construct(
        private Model $model,
        private ?array $data = null,
        private ?int $id = null,
        private ?string $relation = null,
        private bool $storeMode = false,
        private bool $twoModelsStoreMode = false,
        private bool $editMode = false,
        private bool $deleteMode = false,
    ) {
    }

    private ?Model $record = null;



    public function execute(): mixed
    {
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

        foreach ($data as $column => $value) {
            if (!is_object($value) || $column === 'upload_url') {
                $target->{$column} = $this->savableField($column)->setValue($value)->execute();
            } else {
                $target->$column = $value;
            }
        }
        return $target;
    }

    protected function handleStoreMode(): Model|bool
    {

        // Save the main model data
        $model = $this->iterateData($this->data, null); 
        $model = $model->save() ? $this->model : false;

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
        $this->record = $this->model->findOrFail($this->id);
        $record = $this->iterateData($this->data, $this->record);
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
        return $this->model->saveableFields($column);
    }
}
