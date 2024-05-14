<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\CrudException;
use App\Contracts\LocationInterface;
use App\Http\Requests\StatusRequest;
use App\Http\Resources\StatusResource;
use Illuminate\Support\Facades\Config;

class StatusControlller extends Controller
{
    private $statusInterface;

    public function __construct(LocationInterface $statusInterface) {
        $this->statusInterface = $statusInterface;
    }

    public function index()
    {
        try {
            $status = $this->statusInterface->all('Status');
            return StatusResource::collection($status);
        } catch (\Throwable $th) {
            throw CrudException::argumentCountError();
        }
    }

    public function store(StatusRequest $request)
    {
        $validateData = $request->validated();
        try {
          $status = $this->statusInterface->store('Status',$validateData);
            return new StatusResource($status);
        } catch (\Throwable $th) {
            throw CrudException::argumentCountError();
        }
    }


    public function update(StatusRequest $request, string $id)
    {
        $validateData = $request->validated();
        try {
            $this->statusInterface->findById('Status',$id);
            $updatestatus = $this->statusInterface->update('Status',$validateData,$id);
            return new StatusResource($updatestatus);
        } catch (\Throwable $th) {
            throw CrudException::argumentCountError();
        }
    }

    public function destroy(string $id)
    {
        $status = $this->statusInterface->findById('Status',$id);
        if(!$status){
            return response()->json([
                'message'=>Config::get('variable.FAIL_TO_DELETED_STATUS')
            ],Config::get('variable.SEVER_ERROR'));
        }
        $this->statusInterface->delete('Status',$id);
        return response()->json([
            'message'=>Config::get('variable.STATUS_DELTED_SUCCESSFULLY')
        ],Config::get('variable.OK'));
    }
}
