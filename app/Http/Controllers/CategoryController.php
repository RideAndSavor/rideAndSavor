<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\CrudException;
use App\Contracts\LocationInterface;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{

    private $categoryInterface;

    public function __construct(LocationInterface $categoryInterface) {
        $this->categoryInterface = $categoryInterface;
    }
    public function index()
    {
        $category =$this->categoryInterface->all('Category');
        return response()->json(CategoryResource::collection($category)->toArray(request()), 200);

    }

    public function store(CategoryRequest $request)
    {
        $validateData = $request->validated();
       try {
        $catetory = $this->categoryInterface->store('Category',$validateData);
        return new CategoryResource($catetory);
       } catch (\Throwable $th) {
        throw CrudException::argumentCountError();
       }
    }

    public function update(CategoryRequest $request, string $id)
    {
        $validateData = $request->validated();
        $category =$this->categoryInterface->findById('Category',$id);
        if(!$category){
            return response()->json([
                'message'=>Config::get('variable.CATEGORY_NOT_FOUND')
            ],Config::get('variable.SEVER_ERROR'));
        }
        $updateCategory = $this->categoryInterface->update('Category',$validateData,$id);
        return new CategoryResource($updateCategory);
    }

    public function destroy(string $id)
    {
        $category = $this->categoryInterface->findById('Category',$id);
        if(!$category){
            return response()->json([
                'message'=>Config::get('variable.FAIL_TO_DELETED_CATEGORY')
            ],Config::get('variable.SEVER_ERROR'));
        }

       $this->categoryInterface->delete('Category',$id);
        return response()->json([
            'message'=>Config::get('variable.CATEGORY_DELETED_SUCCESSFULLY')
        ],Config::get('variable.NO_CONTENT'));
    }
}
