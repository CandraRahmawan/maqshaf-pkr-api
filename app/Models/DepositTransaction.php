<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Facades\DB;



class DepositTransaction extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;
    
    protected $connection = 'mysql';
    protected $table = 'deposit_transactions';

    public $timestamps = false;



    public static function getAll(){

        $result = DepositTransaction::select('*')
        ->get();

        return $result;
    }

    public static function findById($id){
        $result = DepositTransaction::select('deposit_transaction_id as depositTransactionId', 'transaction_code as transactionCode', 'debet', 'kredit', 'transaction_date as transactionDate', 'created_by as createdBy', 'type', 'deposit_id as depositId')
        ->where('deposit_transaction_id', $id)
        ->get();

        return $result;

    }

    public static function insert($data){

        DB::beginTransaction();
        $depositTransaction = new DepositTransaction();

        try {

            $depositTransaction->transaction_code  = $data['transaction_code'];
            $depositTransaction->debet  = $data['debet'];
            $depositTransaction->kredit = $data['kredit'];        
            $depositTransaction->transaction_date = $data['transaction_date'];
            $depositTransaction->type = $data['type'];
            $depositTransaction->deposit_id = $data['deposit_id'];
            $depositTransaction->created_by = $data['created_by']; 
                                

            $depositTransaction->save();
            $depositTransaction->deposit_transaction_id;
            
            DB::commit();
        } catch (Exception $e) {
            DB:rollback();
        }        

        return $depositTransaction;
    }

    public static function updateData($id, $data){
        DB::beginTransaction();
        $results = false;

        try{
            $result = DepositTransaction::where('deposit_transaction_id', $id)        
            ->update($data);   
            DB::commit();

        }catch (Exception $e){
            DB::rollback();
        }             

        return $result;

    }    
   
}
