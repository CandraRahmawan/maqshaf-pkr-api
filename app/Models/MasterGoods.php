<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Facades\DB;


class MasterGoods extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;
    
    protected $connection = 'mysql';
    protected $table = 'master_goods';

    public $timestamps = false;


    public static function getAll(){

        $result = MasterGoods::select('master_goods_id as masterGoodsId', 'name', 'image', 'description', 'price', 'is_active as isActive', 'code', 'currency', 'category', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy')
        ->orderBy('master_goods_id', 'DESC')
        ->get();

        return $result;
    }

    public static function findById($id){
        $result = MasterGoods::select('master_goods_id as masterGoodsId', 'name', 'image', 'description', 'price', 'is_active as isActive', 'code', 'currency', 'category', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy')
        ->where('master_goods_id', $id)
        ->get();

        return $result;

    }

    public static function insert($data){
        $masterGoods = new MasterGoods();
        
        $masterGoods->name  = $data['name'];        
        $masterGoods->description  = $data['description'];
        $masterGoods->price  = $data['price'];
        $masterGoods->is_active  = $data['is_active'];
        $masterGoods->code  = $data['code'];        
        $masterGoods->created_by  = $data['created_by'];
        $masterGoods->image  = $data['image'];
        
        $masterGoods->save();
        $masterGoods->master_goods_id;

        return $masterGoods;
    }

    public static function updateData($id, $data){
        DB::beginTransaction();        
        $results = false;

        try{
            $result = MasterGoods::where('master_goods_id', $id)        
            ->update($data);   
            DB::commit();

        }catch (Exception $e){
            DB::rollback();
        }             

        return $result;

    }    

    public static function findByName($name){
        $result = MasterGoods::select('master_goods_id as masterGoodsId', 'name', 'image', 'description', 'price', 'is_active as isActive', 'code', 'currency', 'category', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy')
        ->where('name', 'like', '%' . $name . '%')
        ->orderBy('master_goods_id', 'DESC')
        ->get();

        return $result;

    }
    
}
