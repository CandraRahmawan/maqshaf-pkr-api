<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Facades\DB;
use App\Models\TransactionItem;


class Transactions extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;
    
    protected $connection = 'mysql';
    protected $table = 'transactions';
    public $timestamps = false;


    public static function getAll(){

        $result = Transactions::select('transaction_id as transactionId', 'transaction_code as transactionCode', 'transaction_date as transactionDate', 'total', 'qty', 'user_id as userId')
        ->get();

        return $result;
    }

    public static function findById($id){
        $result = Transactions::select('transaction_id as transactionId', 'transaction_code as transactionCode', 'transaction_date as transactionDate', 'total', 'qty', 'user_id as userId')
        ->where('transaction_id', $id)
        ->get();

        return $result;

    }

    public static function insert($data){
        $transactions = new Transactions();
        
        $transactions->transaction_code  = $data['transaction_code'];        
        $transactions->transaction_date  = $data['transaction_date'];
        $transactions->total  = $data['total'];
        $transactions->qty  = $data['qty'];
        $transactions->user_id  = $data['user_id']; 

        $transactions->save();
        $transactions->transaction_id;

        return $transactions;
    }

    public static function updateData($id, $data){
        DB::beginTransaction();        
        $results = false;

        try{
            $result = MasterGoods::where('transaction_id', $id)        
            ->update($data);   
            DB::commit();

        }catch (Exception $e){
            DB::rollback();
        }             

        return $result;

    }

    public static function buyItem($dataTransaction){
        DB::beginTransaction();
        $results = false;

        try{
            $resultTransaction = static::insert($dataTransaction);

            foreach ($dataTransaction['items'] as $value) {
                $dataTransactionItem = array(
                    "transaction_id" => $resultTransaction->id,
                    "price" => $value['price'],
                    "name" => $value['name'],
                    "qty" => $value['qty']
                );

                // print_r($dataTransactionItem);

               TransactionItem::insert($dataTransactionItem);
            }
            DB::commit();

            // die();
            // $results = true;
            $results = $resultTransaction;

        }catch (Exception $e){
            DB::rollback();
        }

        return $results;
    }
   
}
