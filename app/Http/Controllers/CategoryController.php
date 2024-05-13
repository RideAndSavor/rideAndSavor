<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use App\Contracts\LocationInterface;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\Config;

class CategoryController extends Controller
{

    private $categoryInterface;

    public function __construct(LocationInterface $categoryInterface) {
        $this->categoryInterface = $categoryInterface;
    }
    public function index()
    {
        $category =$this->categoryInterface->all('Category');
        return CategoryResource::collection($category);

    }

    public function store(CategoryRequest $request)
    {
        $validateData = $request->validated();
        // dd($validateData);
        $catetory = $this->categoryInterface->store('Category',$validateData);
        if(!$catetory){
            return response()->json([
                'message'=>Config::get('variable.CATEGORY_NOT_FOUND')
            ],Config::get('variable.CLIENT_ERROR'));
        }
        return new CategoryResource($catetory);
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
        ]);
    }
}
