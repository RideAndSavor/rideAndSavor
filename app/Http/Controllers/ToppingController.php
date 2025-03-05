<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\CrudException;
use App\Contracts\LocationInterface;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\ToppingRequest;
use App\Http\Resources\ToppingResource;

class ToppingController extends Controller
{
    private $toppingInterface;

    public function __construct(LocationInterface $toppingInterface) {
        $this->toppingInterface = $toppingInterface;
    }

    public function index()
    {
        $topping = $this->toppingInterface->all('Topping');
        return response()->json(ToppingResource::collection($topping)->toArray(request()), 200);
    }


    public function store(ToppingRequest $request)
    {
        $validateData = $request->validated();
       try {
        $topping = $this->toppingInterface->store('Topping',$validateData);
        return new ToppingResource($topping);
       } catch (\Throwable $th) {
        throw CrudException::argumentCountError();
       }
    }


    public function update(ToppingRequest $request, string $id)
    {
        $validateData = $request->validated();
        // dd($validateData);


        $topping = $this->toppingInterface->findById('Topping',$id);
        if(!$topping){
            return response()->json([
                'message'=>Config::get('variable.TOPPINGS_NOT_FOUND')
            ],Config::get('variable.SEVER_ERROR'));
        }
        $updateTopping = $this->toppingInterface->update('Topping',$validateData,$id);
        return new ToppingResource($updateTopping);
    }

    public function destroy(string $id)
    {
        // dd($id);
        $topping = $this->toppingInterface->findById('Topping',$id);
        // dd($topping);
        if(!$topping){
            return response()->json([
                'message'=>Config::get('variable.FAIL_TO_DELETED_TOPPING')
            ],Config::get('variable.SEVER_ERROR'));
        }

        $this->toppingInterface->delete('Topping',$id);
        // dd($this->toppingInterface);
        return response()->json([
            'message'=>Config::get('variable.TOPPING_DELETED_SUCCESSFULLY')
        ],Config::get('variable.NO_CONTENT'));
    }
}
