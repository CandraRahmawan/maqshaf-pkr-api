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

        return $response;
    }

    public static function responseWithPage($code, $page, $limit, $count, $data = []){
        $response = [
            "code" => $code, 
            "message" => $code == 200 ? "Success" : "Bad Request", 
            "data" => $code == 200 ? $data : [], 
            "pageSummary" => [
                "page" => $page, 
                "limit" => $limit, 
                "totalCount" => $count 
            ] 
        ]; 


        return $response;
    }

    public static function responseWithMessage($code, $message, $data = []){           
        $response = [
            "code" => $code, 
            "message" => $message,
            "data" => $code == 200 ? $data : [],
        ]; 

        return $response;
    }


}
