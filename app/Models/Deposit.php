<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Support\Facades\DB;
use App\Models\DepositTransaction;


class Deposit extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;
    
    protected $connection = 'mysql';
    protected $table = 'deposit';


    public static function getAll(){

        $result = Deposit::select('deposit_id as depositId', 'user_id as userId', 'saldo', 'previous_saldo as previousSaldo', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy')
        ->get();

        return $result;
    }

    public static function findById($id){
        $result = Deposit::select('deposit_id as depositId', 'user_id as userId', 'saldo', 'previous_saldo as previousSaldo', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy')
        ->where('deposit_id', $id)
        ->get();

        return $result;

    }

    public static function findByUserId($id){
        $result = Deposit::select('deposit_id as depositId', 'user_id as userId', 'saldo', 'previous_saldo as previousSaldo', 'created_at as createdAt', 'created_by as createdBy', 'updated_at as updatedAt', 'updated_by as updatedBy')
        ->where('user_id', $id)
        ->get();

        return $result;

    }

    public static function insert($data){
        DB::beginTransaction();
        $deposit = new Deposit();
        
        try {
            
            $deposit->user_id  = $data['user_id'];
            $deposit->saldo  = $data['saldo'];
            $deposit->previous_saldo = $data['previous_saldo'];        
            $deposit->created_at = $data['created_at'];
            $deposit->created_by = $data['created_by'];

            $deposit->save();
            $deposit->deposit_id;

            DB::commit();
        } catch (Exception $e) {
            DB:rollback();
        }
        

        return $deposit;
    }

    public static function updateData($id, $data){
        DB::beginTransaction();        
        $results = false;

        try{
            $result = Deposit::where('deposit_id', $id)        
            ->update($data);   
            DB::commit();

        }catch (Exception $e){
            DB::rollback();
        }             

        return $result;

    }    

    public static function debetOrKredit($depositId, $dataDepositForUpdate, $dataDepositTransaction){
        DB::beginTransaction();        
        $results = false;

        try{
            // $result = Deposit::where('deposit_id', $depositId)        
            // ->update($dataDepositForUpdate);
            $result = static::updateData($depositId, $dataDepositForUpdate);
            

            $depositTransaction = new DepositTransaction();
            $depositTransaction->transaction_code  = $dataDepositTransaction['transaction_code'];
            $depositTransaction->debet  = $dataDepositTransaction['debet'];
            $depositTransaction->kredit = $dataDepositTransaction['kredit'];        
            $depositTransaction->transaction_date = $dataDepositTransaction['transaction_date'];
            $depositTransaction->type = $dataDepositTransaction['type'];
            $depositTransaction->deposit_id = $dataDepositTransaction['deposit_id'];
            $depositTransaction->created_by = $dataDepositTransaction['created_by'];

            // DepositTransaction::save();
            DepositTransaction::insert($dataDepositTransaction);

            DB::commit();

        }catch (Exception $e){
            DB::rollback();
        }             

        return $result;

    }

    public static function firstKredit($data){
        DB::beginTransaction();        
        $results = false;

        try{

            // $resultDeposit = $this->insert($dataDeposit);
            $resultDeposit = static::insert($data);

            $dataDepositTransaction = array(
                    'transaction_code' => $data['transaction_code'],
                    'debet' => 0,
                    'kredit' => $data['kredit'],
                    'transaction_date' => $data['created_at'],
                    'created_by' => $data['created_by'],
                    'type' => '3',
                    'deposit_id' => $resultDeposit->first()->deposit_id

                );

            $result = DepositTransaction::insert($dataDepositTransaction);

            DB::commit();

        }catch (Exception $e){
            DB::rollback();
        }

        return $result;

    }
   
}
