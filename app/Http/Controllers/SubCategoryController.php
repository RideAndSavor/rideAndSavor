<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\CrudException;
use App\Contracts\LocationInterface;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\SubCategoryRequest;
use App\Http\Resources\SubCategoryResource;

class SubCategoryController extends Controller
{

    private $subcategoryInterface;

    public function __construct(LocationInterface $subcategoryInterface) {
        $this->subcategoryInterface = $subcategoryInterface;
    }
    public function index()
    {
        $subcategory = $this->subcategoryInterface->all('SubCategory');
        return SubCategoryResource::collection($subcategory);
    }

    public function store(SubCategoryRequest $request)
    {
        $validateData = $request->validated();
      try {
        $subcategory = $this->subcategoryInterface->store('SubCategory',$validateData);
        return new SubCategoryResource($subcategory);
      } catch (\Throwable $th) {
        throw CrudException::argumentCountError();
      }
    }

    public function update(SubCategoryRequest $request, string $id)
    {
        $validateData = $request->validated();
        $subCatgory = $this->subcategoryInterface->findById('SubCategory',$id);
        if(!$subCatgory){
            return response()->json([
                'message'=>Config::get('variable.SUBCATEGORY_NOT_FOUND')
            ],Config::get('variable.SEVER_ERROR'));
        }
        $updateSubCategory = $this->subcategoryInterface->update('SubCategory',$validateData,$id);
        return new SubCategoryResource($updateSubCategory);
    }

    public function destroy(string $id)
    {
        $subCategory = $this->subcategoryInterface->findById('SubCategory',$id);

        if(!$subCategory){
            return response()->json([
                'message'=>Config::get('variable.FAIL_TO_DELETED_SUBCATEGORY')
            ],Config::get('variable.SEVER_ERROR'));
        }

        $this->subcategoryInterface->delete('SubCategory',$id);
        return response()->json([
            'message'=>Config::get('variable.SUBCATEGORY_DELETED_SUCCESSFULLY')
        ]);
    }
}
