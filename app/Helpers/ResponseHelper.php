<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;

class ResponseHelper
{
    public static function jsonResponseWithConfigError($e)
    {
        return response()->json([
            'message' => Config::get('variable.ISE'),
            'error' => $e->getMessage()
        ], Config::get('variable.SEVER_ERROR')); //500
    }

    public static function jsonResponseWithClientError($e)
    {
        return response()->json([
            'message' => Config::get('variable.CLIENT_ERROR'), //401
            'error' => $e->getMessage()
        ], Config::get('variable.SEVER_ERROR')); //500
    }


}
