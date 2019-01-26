<?php
namespace App\Helpers;

class ApiResponse
{
     public static function response($data='')
     {
        return response()->json([
            'result' => $data
        ]);
     }
}