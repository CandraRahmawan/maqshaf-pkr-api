<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Facades\DB;


class TransactionItem extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;
    
    protected $connection = 'mysql';
    protected $table = 'transaction_items';
    public $timestamps = false;


    public static function getAll(){

        $result = TransactionItem::select('transaction_items_id as transactionItemsId', 'transaction_id as transactionId', 'price', 'name', 'qty')
        ->get();

        return $result;
    }

    public static function findById($id){
        $result = TransactionItem::select('transaction_items_id as transactionItemsId', 'transaction_id as transactionId', 'price', 'name', 'qty')
        ->where('transaction_items_id', $id)
        ->get();

        return $result;

    }

    public static function insert($data){

        DB::beginTransaction();
        $transactionItem = new TransactionItem();

        try {
            $transactionItem->transaction_id  = $data['transaction_id'];        
            $transactionItem->price  = $data['price'];
            $transactionItem->name  = $data['name'];
            $transactionItem->qty  = $data['qty'];                   

            $transactionItem->save();
            $transactionItem->transaction_items_id;

            DB::commit();
        } catch (Exception $e) {
            DB:rollback();
        }        

        return $transactionItem;
    }

    public static function updateData($id, $data){
        DB::beginTransaction();        
        $results = false;

        try{
            $result = MasterGoods::where('transaction_items_id', $id)        
            ->update($data);   
            DB::commit();

        }catch (Exception $e){
            DB::rollback();
        }             

        return $result;

    }    
   
}
