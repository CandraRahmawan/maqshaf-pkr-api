<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Facades\DB;


class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;
    
    protected $connection = 'mysql';
    protected $table = 'users';
    public $timestamps = false;

    public static function findAll($limit=5){
        $results = User::select('user_id as userId', 'nis', 'full_name as fullName', 'class', 'address', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy', 'is_delete as isDelete', 'deleted_at as deletedAt', 'deleted_by as deletedBy')        
        ->where('is_delete', null)
        ->orderBy('user_id', 'DESC')
        ->paginate($limit);

        return $results;
    }

    public static function findById($id){
        $results = User::select('user_id as userId', 'nis', 'full_name as fullName', 'class', 'address', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy', 'is_delete as isDelete', 'deleted_at as deletedAt', 'deleted_by as deletedBy')
        ->where('is_delete', null)
        ->where('user_id', $id)
        ->get();

        return $results;
    }

    public static function findByIdAndPin($id, $pin){
        $results = User::select('user_id as userId', 'nis', 'full_name as fullName', 'class', 'address', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy', 'is_delete as isDelete', 'deleted_at as deletedAt', 'deleted_by as deletedBy')
        ->where('user_id', $id)
        ->where('pin', $pin)
        ->get();

        return $results;

    }

    public static function insert($data){
        $user = new User();

        $user->nis = $data['nis'];
        $user->full_name  = $data['full_name'];
        $user->class  = $data['class'];
        $user->address = $data['address'];
        $user->pin = $data['pin'];
        $user->created_by = $data['created_by'];

        // User::create(['nis' => 'nis 02']);
        $user->save();
        $user->user_id;

        return $user;
    }

    public static function updateData($id, $data){
        try {
            $user = User::where('user_id', $id)        
            ->update($data);            
        } catch (Exception $e) {
            $user = null;
        }

        return $user;
    }

    public static function updatePin($id, $pinOld, $data){
        DB::beginTransaction();        
        $results = false;

        try 
        { 
            $results = User::where('user_id', $id) 
            ->where('pin', $pinOld)       
            ->update($data); 
            DB::commit();
            
        }
        catch (Exception $e) 
        {            
            DB::rollback();

        }        

        return $results;

    }

    public static function findByNameAndClass($name, $class){
        $results = User::select('user_id as userId', 'nis', 'full_name as fullName', 'class', 'address', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy', 'is_delete as isDelete', 'deleted_at as deletedAt', 'deleted_by as deletedBy')
        ->where('is_delete', null)
        ->where('full_name', 'like', '%' . $name . '%')
        ->where('class', $class)
        ->get();

        return $results;

    }

    public static function findByNis($nis){
        $results = User::select('user_id as userId', 'nis', 'full_name as fullName', 'class', 'address', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy', 'is_delete as isDelete', 'deleted_at as deletedAt', 'deleted_by as deletedBy')
        ->where('is_delete', null)
        ->where('nis', $nis)
        ->get();

        return $results;

    }

    public static function findByNameAndNis($limit, $name, $nis){

        $results = User::select('users.user_id as userId', 'users.nis', 'users.full_name as fullName', 'users.class', 'users.address', 'users.created_at as createdAt', 'users.created_by as createdBy', 'users.updated_at as updatedAt', 'users.updated_by as updatedBy', 'users.is_delete as isDelete', 'users.deleted_at as deletedAt', 'users.deleted_by as deletedBy', 'deposit.saldo')
        ->leftjoin('deposit', 'deposit.user_id', '=', 'users.user_id')
        ->where('users.nis', 'like', '%'. $nis . '%')
        ->where('users.full_name','like', '%' . $name . '%')        
        ->where('users.is_delete', null)
        ->orderBy('users.user_id', 'DESC')
        ->paginate($limit);        

        return $results;
    }

    public static function findByName($limit, $name){
        $results = User::select('user_id as userId', 'nis', 'full_name as fullName', 'class', 'address', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy', 'is_delete as isDelete', 'deleted_at as deletedAt', 'deleted_by as deletedBy')
        ->where('is_delete', null)
        ->where('full_name', 'like', '%' . $name . '%')
        ->paginate($limit);

        return $results;

    }

    public static function findByNisPaging($limit, $nis){
        $results = User::select('user_id as userId', 'nis', 'full_name as fullName', 'class', 'address', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy', 'is_delete as isDelete', 'deleted_at as deletedAt', 'deleted_by as deletedBy')
        ->where('is_delete', null)
        ->where('nis', $nis)
        ->paginate($limit);

        return $results;

    }

    public static function findAllUserAndDeposit($limit=5){
        $results = User::select('users.user_id as userId', 'users.nis', 'users.full_name as fullName', 'users.class', 'users.address', 'users.created_at as createdAt', 'users.created_by as createdBy', 'users.updated_at as updatedAt', 'users.updated_by as updatedBy', 'users.is_delete as isDelete', 'users.deleted_at as deletedAt', 'users.deleted_by as deletedBy', 'deposit.saldo')
        ->leftjoin('deposit', 'deposit.user_id', '=', 'users.user_id')
        ->where('users.is_delete', null)
        ->orderBy('users.user_id', 'DESC')
        ->paginate($limit);

        return $results;
    }


   
}
