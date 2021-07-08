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
    public $timestamps = false;

    public static function findAll(){
        $results = User::select('user_id as userId', 'nis', 'full_name as fullName', 'class', 'address', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy')
            // ->where('nis', '1111')
            ->get();

        return $results;
    }

    public static function findById($id){
        $results = User::select('user_id as userId', 'nis', 'full_name as fullName', 'class', 'address', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy')
            ->where('user_id', $id)
            ->get();

        return $results;
    }

    public static function findByIdAndPin($id, $pin){
        $results = User::select('user_id as userId', 'nis', 'full_name as fullName', 'class', 'address', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy')
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
        $user->pin = sha1($data['pin']);
        $user->created_by = $data['created_by'];

        // User::create(['nis' => 'nis 02']);
        $user->save();
        $user->user_id;

        return $user;
    }

    public static function updateData($id, $data){
        $user = User::where('user_id', $id)
            ->update($data);

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



}
