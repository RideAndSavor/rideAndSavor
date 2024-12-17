<?php

namespace App\Traits;

trait SearchTrait
{
    public function searchModels(array $models, array $terms)
    {
        $response = [];

        foreach ($models as $key => $modelConfig) {
            $model = $modelConfig[0];
            $searchableFields = $modelConfig[1];

            $results = $model::where(function ($query) use ($terms, $searchableFields) {
                foreach ($terms as $term) {
                    foreach ($searchableFields as $field) {
                        $query->orWhere($field, 'like', '%' . $term . '%');
                    }
                }
            })->get();

            if ($results->isNotEmpty()) {
                $response[$key] = $results;
            }
        }

        return $response;
    }
}
