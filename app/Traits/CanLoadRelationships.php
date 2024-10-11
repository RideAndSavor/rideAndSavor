<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

trait CanLoadRelationships
{
  public function loadRelationships(
    Model|QueryBuilder|EloquentBuilder $for,
    ?array $relations = null
  ): Model|QueryBuilder|EloquentBuilder {
    $relations = $relations ?? $this->relations ?? [];
    foreach ($relations as $relation) {
      $for->when(
        $this->shouldIncludeRelation($relation),
        function ($q) use ($relation) {
          if ($relation == 'ratings') {
            $q->withAvg($relation, 'rating_id');
          }
          $q->with($relation);
        }
        // }
      );
    }
    return $for;
  }

  protected function shouldIncludeRelation(string $relation): bool
  {
    $include = request()->query('include');
    $relations = array_map('trim', explode(',', $include));
    if (!$include) {
      return false;
    }

    return in_array($relation, $relations);
  }
}
