<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\CrudException;
use App\Contracts\LocationInterface;
use App\Http\Requests\SalaryRequest;
use App\Http\Resources\SalaryResource;
use Illuminate\Support\Facades\Config;

class SalaryController extends Controller
{
    private $salaryInterface;

    public function __construct(LocationInterface $salaryInterface) {
        $this->salaryInterface = $salaryInterface;
    }

    public function index()
    {
        try {
            $salary = $this->salaryInterface->all('Salary');
            return SalaryResource::collection($salary);
        } catch (\Throwable $th) {
            throw CrudException::argumentCountError();
        }
    }

    public function store(SalaryRequest $request)
    {
        $validateData = $request->validated();
        try {
           $salary=$this->salaryInterface->store('Salary',$validateData);
            return new SalaryResource ($salary);
        } catch (\Throwable $th) {
            throw CrudException::argumentCountError();
        }
    }


    public function update(SalaryRequest $request, string $id)
    {
        $validateData = $request->validated();
        try {
            $this->salaryInterface->findById('Salary',$id);
            $updateSalary = $this->salaryInterface->update('Salary',$validateData,$id);
            return new SalaryResource($updateSalary);
        } catch (\Throwable $th) {
            throw CrudException::argumentCountError();
        }
    }

    public function destroy(string $id)
    {
        $salary = $this->salaryInterface->findById('Salary',$id);
        if(!$salary){
            return response()->json([
                'message'=>Config::get('variable.FAILED_TO_DELETED_SALARY')
            ],Config::get('variable.SEVER_ERROR'));
        }
        $this->salaryInterface->delete('Salary',$id);
        return response()->json([
            'message'=>Config::get('variable.SALARY_DELETED_SUCCESSFULLY')
        ],Config::get('variable.NO_CONTENT'));
    }
}
