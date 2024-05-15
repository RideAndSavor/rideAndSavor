<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\CrudException;
use App\Http\Requests\RoleRequest;
use App\Contracts\LocationInterface;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Config;

class RoleController extends Controller
{
    private $roleInterface;

    public function __construct(LocationInterface $roleInterface) {
        $this->roleInterface = $roleInterface;
    }

    public function index()
    {
        try {
          $role = $this->roleInterface->all('Role');
          return RoleResource::collection($role);
        } catch (\Throwable $th) {
            throw CrudException::argumentCountError();
        }
    }


    public function store(RoleRequest $request)
    {
        $validateData = $request->validated();
        try {
            $role = $this->roleInterface->store('Role',$validateData);
            return new RoleResource($role);
        } catch (\Throwable $th) {
            throw CrudException::argumentCountError();
        }
    }

    public function update(RoleRequest $request, string $id)
    {
        $validateData = $request->validated();
        try {
            $this->roleInterface->findById('Role',$id);
            $updateRole = $this->roleInterface->update('Role',$validateData,$id);
            return new RoleResource($updateRole);
        } catch (\Throwable $th) {
            throw CrudException::argumentCountError();
        }
    }


    public function destroy(string $id)
    {
       $role = $this->roleInterface->findById('Role',$id);
       if(!$role){
        return response()->json([
        'message'=>Config::get('variable.FAIL_TO_DELETED_ROLE')
        ],Config::get('variable.SEVER_ERROR'));
       }

       $this->roleInterface->delete('Role',$id);
       return response()->json([
        'message'=>Config::get('variable.ROLE_DELETED_SUCCESSFULLY')
       ],Config::get('variable.NO_CONTENT'));
    }
}

