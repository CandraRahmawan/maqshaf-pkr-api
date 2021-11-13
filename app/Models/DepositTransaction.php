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
        $result = DepositTransaction::select('deposit_transaction_id as depositTransactionId', 'transaction_code as transactionCode', 'debet', 'kredit', 'transaction_date as transactionDate', 'created_by as createdBy', 'type', 'deposit_id as depositId', 'transaction_id as transactionId')
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
            $depositTransaction->transaction_id = $data['transaction_id'];
                                

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

    public static function getAllKreditByYearAndMonth($limit = 10, $year, $month){
        $result = DepositTransaction::select('*')        
        ->where('type', 3)
        ->whereYear('transaction_date', $year)
        ->whereMonth('transaction_date', $month)
        ->orderBy('deposit_transaction_id', 'DESC')
        ->paginate($limit);

        return $result;
    }

    public static function getAllDebitByYearAndMonth($limit = 10, $year, $month){
        $result = DepositTransaction::select('*')
        ->join('transactions', 'transactions.transaction_id','=', 'deposit_transactions.transaction_id')
        ->join('users', 'users.user_id', '=', 'transactions.user_id')
        ->where('type', 1)
        ->whereYear('deposit_transactions.transaction_date', $year)
        ->whereMonth('deposit_transactions.transaction_date', $month)
        ->orderBy('deposit_transaction_id', 'DESC')
        ->paginate($limit);

        return $result;
    }

    public static function getAllKreditFindByTrxCOde($limit = 10, $trxCode, $year, $month){
        $result = DepositTransaction::select('*')        
        ->where('type', 3)
        ->whereYear('transaction_date', $year)
        ->whereMonth('transaction_date', $month)
        ->where('transaction_code', 'like', '%' . $trxCode . '%')
        ->orderBy('deposit_transaction_id', 'DESC')
        ->paginate($limit);

        return $result;
    }

    public static function getDebitByNisOrTransactionCode($limit = 10, $nis, $trxCode, $year, $month){
        $result = DepositTransaction::select('*')
        ->join('transactions', 'transactions.transaction_id','=', 'deposit_transactions.transaction_id')
        ->join('users', 'users.user_id', '=', 'transactions.user_id')
        ->where('type', 1)
        ->whereYear('deposit_transactions.transaction_date', $year)
        ->whereMonth('deposit_transactions.transaction_date', $month)
        ->where('transactions.transaction_code', 'like', '%' . $trxCode . '%')
        ->where('users.nis', 'like', '%' . $nis . '%')
        ->orderBy('deposit_transaction_id', 'DESC')
        ->paginate($limit);

        return $result;
    }

    public static function getAllKreditAmountByYearAndMonth($year, $month){
        $result = DepositTransaction::select('sum')        
        ->where('type', 3)
        ->whereYear('transaction_date', $year)
        ->whereMonth('transaction_date', $month)
        ->sum('kredit');
        

        return $result;
    }

    public static function getAllDebitAmountByYearAndMonth($year, $month){
        $result = DepositTransaction::select('sum')
        ->join('transactions', 'transactions.transaction_id','=', 'deposit_transactions.transaction_id')
        ->join('users', 'users.user_id', '=', 'transactions.user_id')
        ->where('type', 1)
        ->whereYear('deposit_transactions.transaction_date', $year)
        ->whereMonth('deposit_transactions.transaction_date', $month)
        ->sum('debet');

        return $result;
    }

    public static function getAllKredit($limit = 1){
        $result = DepositTransaction::select('*')        
        ->where('type', 3)        
        ->orderBy('deposit_transaction_id', 'DESC')
        ->paginate($limit);

        return $result;
    }

    public static function getAllKreditAmount(){
        $result = DepositTransaction::select('sum')        
        ->where('type', 3)        
        ->sum('kredit');
        

        return $result;
    }

    public static function getAllDebit($limit = 1){
        $result = DepositTransaction::select('*')
        ->join('transactions', 'transactions.transaction_id','=', 'deposit_transactions.transaction_id')
        ->join('users', 'users.user_id', '=', 'transactions.user_id')
        ->where('type', 1)        
        ->orderBy('deposit_transaction_id', 'DESC')
        ->paginate($limit);
        

        return $result;
    }
   
   public static function getAllDebitAmount(){
        $result = DepositTransaction::select('sum')
        ->join('transactions', 'transactions.transaction_id','=', 'deposit_transactions.transaction_id')
        ->join('users', 'users.user_id', '=', 'transactions.user_id')
        ->where('type', 1)        
        ->sum('debet');

        return $result;
    }

    public static function getAllWithDrawl($limit = 1){
        $result = DepositTransaction::select('*')        
        ->join('deposit', 'deposit.deposit_id', '=', 'deposit_transactions.deposit_id')
        ->join('users', 'users.user_id', '=', 'deposit.user_id')
        ->where('deposit_transactions.type', 2)        
        ->orderBy('deposit_transaction_id', 'DESC')
        ->paginate($limit);
        

        return $result;
    }

    public static function getAllWithDrawlAmount(){
        $result = DepositTransaction::select('sum')        
        ->join('deposit', 'deposit.deposit_id', '=', 'deposit_transactions.deposit_id')
        ->join('users', 'users.user_id', '=', 'deposit.user_id')
        ->where('deposit_transactions.type', 2)        
        ->orderBy('deposit_transaction_id', 'DESC')
        ->sum('debet');
        

        return $result;
    }

    public static function printAllDebitByYearAndMonth($year, $month, $limit = 100000){
        $result = DepositTransaction::select('deposit_transactions.transaction_code', 'deposit_transactions.transaction_date', 'deposit_transactions.debet', 'users.nis', 'users.full_name', 'users.class')
        ->join('transactions', 'transactions.transaction_id','=', 'deposit_transactions.transaction_id')
        ->join('users', 'users.user_id', '=', 'transactions.user_id')
        ->where('type', 1)
        ->whereYear('deposit_transactions.transaction_date', $year)
        ->whereMonth('deposit_transactions.transaction_date', $month)        
        ->orderBy('deposit_transaction_id', 'DESC')
        ->paginate($limit);

        return $result;
    }

    public static function printAllKreditFindByTrxCOde($year, $month, $limit = 100000){
        $result = DepositTransaction::select('deposit_transactions.transaction_code', 'deposit_transactions.kredit', 'deposit_transactions.transaction_date', 'users.nis', 'users.full_name', 'users.class')
        ->join('deposit', 'deposit.deposit_id', '=', 'deposit_transactions.deposit_id')
        ->join('users', 'users.user_id', '=', 'deposit.user_id')
        ->where('type', 3)
        ->whereYear('transaction_date', $year)
        ->whereMonth('transaction_date', $month)        
        ->orderBy('deposit_transaction_id', 'DESC')
        ->paginate($limit);

        return $result;
    }
    
}
