<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;



class Response extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    public static function response($code, $data = []){           
        $response = [
            "code" => $code, 
            "message" => $code == 200 ? "Success" : "Bad Request", 
            "data" => $code == 200 ? $data : [],
        ]; 

        // return $response;
        return response($response, $code);
    }

    public static function responseWithPage($code, $data = [], $pageSummary){
        $response = [
            "code" => $code, 
            "message" => $code == 200 ? "Success" : "Bad Request", 
            "data" => $code == 200 ? $data : [], 
            "pageSummary" => $pageSummary
        ]; 


        // return $response;
        return response($response, $code);
    }

    public static function responseWithMessage($code, $message, $data = []){           
        $response = [
            "code" => $code, 
            "message" => $message,
            "data" => $code == 200 ? $data : [],
        ]; 

        // return $response;
        return response($response, $code);
    }

    public static function responseWithoutArray($code, $data = null){           
        $response = [
            "code" => $code, 
            "message" => $code == 200 ? "Success" : "Bad Request", 
            "data" => $code == 200 ? $data[0] : null,
        ]; 

        // return $response;
        return response($response, $code);
    }


}
