<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Facades\DB;


class Administrator extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;
    
    protected $connection = 'mysql';
    protected $table = 'administrator';


    public static function getAll(){

        $result = Administrator::select('administrator_id as administratorId', 'full_name as fullName', 'username', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy', 'delete_at as deleteAt', 'delete_by as deleteBy')
        ->get();

        return $result;
    }

    public static function findById($id){
        $result = Administrator::select('administrator_id as administratorId', 'full_name as fullName', 'username', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy', 'delete_at as deletedAt', 'delete_by as deletedBy')
        ->where('administrator_id', $id)
        ->get();

        return $result;

    }

    public static function insert($data){
        $administrator = new Administrator();
        
        $administrator->full_name  = $data['full_name'];
        $administrator->password  = $data['password'];
        $administrator->username = $data['username'];        
        $administrator->created_by = $data['created_by'];
        
        $administrator->save();
        $administrator->user_id;

        return $administrator;
    }

    public static function updateData($id, $data){
        DB::beginTransaction();        
        $results = false;

        try{
            $result = Administrator::where('administrator_id', $id)        
            ->update($data);   
            DB::commit();

        }catch (Exception $e){
            DB::rollback();
        }             

        return $result;

    }


    public static function updatePassword($id, $passOld, $data){
        DB::beginTransaction();        
        $results = false;        

        try{
            $result = Administrator::where('username', $data['username'])
            ->where('password', $passOld)             
            ->update($data);   

            DB::commit();

        }catch (Exception $e){
            DB::rollback();
        }             

        return $result;

    }

    public static function findByUsernameAndPassword($username, $password){
        $result = Administrator::select('administrator_id as administratorId', 'full_name as fullName', 'username', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy', 'delete_at as deletedAt', 'delete_by as deletedBy')
        ->where('username', $username)
        ->where('password', $password)
        ->get();

        return $result;
    }
   
}
