<?php

namespace App\Repositories;

use App\Db\Core\Crud;
use App\Helper\ReadOnlyArray;
use App\Contracts\BaseInterface;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Support\Facades\Config;

class BaseRepository implements BaseInterface
{
  protected $currentModel;
  protected $currentTable;
  public $imageTable;
  protected $data;

  public function __construct(string $modelName)
  {
    if (!empty($modelName)) {
      $this->currentModel = app("App\\Models\\{$modelName}");
      $this->currentTable = $this->currentModel->getTable();
    }

    $this->imageTable = Config::get('variables.IMAGE_MODEL');
  }

  public function all()
  {
    $this->currentModel->all();
  }

  public function getById($id)
  {
    return $this->currentModel->find($id);
  }

  public function getByName($name)
  {
    $this->currentModel::Name($name)->get();
  }

  public function getDataWithRelation(string $relationModel)
  {
    return $this->currentModel->with($relationModel);
  }

  public function store(array $data, string $imageDir = null, string $diskName = null)
  {
    $this->currentModel = new $this->currentModel;
    new ReadOnlyArray($data);
    /* Data Preparation */
    $crud = new Crud(model: $this->currentModel, data: $data, storeMode: true);
    /* Data Execution */
    return $crud->execute();
  }

  public function update(array $data, int $id, string $imageDir = null, string $diskName = null)
  {
    new ReadOnlyArray($data);
    /* Data Preparation */
    $crud = new Crud(model: $this->currentModel, data: $data, id: $id, editMode: true);
    /* Data Execution */
    return $crud->execute();
  }

  public function delete(int $id)
  {
    return (new Crud(model: $this->currentModel, id: $id,  deleteMode: true))->execute();
  }

  public function twoModelsStore(int $id, string $relation, array $data)
  {
    return (new Crud(model: $this->currentModel, data: $data, id: $id, relation: $relation, twoModelsStoreMode: true))->execute();
  }

  public function MorphStore(Model $parentModel, array $data, $relationName)
  {
    // Get the name of the morph relation
    return $parentModel->$relationName()->createMany($data);
  }
}