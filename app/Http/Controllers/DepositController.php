<?php

namespace App\Http\Controllers;
use App\Models\Deposit;
use App\Models\DepositTransaction;
use App\Models\TransactionItem;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Response;
use DateTime;

class DepositController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    

    public function findAll(){        

        // $data = Deposit::getAll();
        $data = DepositTransaction::getAll();
        $ress = Response::response(200, $data);
        
        return $ress;
    }

    public function findById($id){        
        // $data = Deposit::findById($id);
        $data = Deposit::findByUserId($id);
        
        $ress = Response::response(200, count($data));

        return $ress;
    }    

    public function insert(Request $request){
        $data = array(            
            'user_id'  => $request->input('userId'),
            'saldo' => $request->input('saldo'),
            'previous_saldo'  => $request->input('previousSaldo'),
            'created_at'  => $request->input('createdAt'),
            'created_by' => $request->input('createdBy')            
        );

        $save = Deposit::insert($data);
        
        $ress = Response::response(200, $save);

        return $ress;

    }

    public function updateData(Request $request, $id){        
        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s');

        $data = array(
            'full_name'  => $request->input('fullName'),            
            'username'  => $request->input('username'),         
            'updated_by' => $request->input('updatedBy'),
            'updated_at' => $now
        );

        $update = Deposit::updateData($id, $data);
        $code = $update ? 200 : 400;
        $ress = Response::response($code);

        return $ress;
    }    

    public function deleteDataById(Request $request, $id) {

        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s');

        $data = array(                   
            'delete_by' => $request->input('deleteBy'),
            'delete_at' => $now
        );

        $delete = Deposit::updateData($id, $data);
        $code = $delete ? 200 : 400;
        $ress = Response::response($code);

        return $ress;

    }

    public function generateTransactionCode()
    {
        $t = microtime(true);
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
        $nowMilisecond = $d->format("Y-m-d H:i:s.u");

        $replaceOne = str_replace("-","",$nowMilisecond);
        $replaceTwo = str_replace(" ","",$replaceOne);
        $replaceThree = str_replace(":","",$replaceTwo);
        $replaceFour = str_replace(".","",$replaceThree);

        return $replaceFour;
    }

    public function debit(Request $request, $userId){

        $code = 400;

        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s');        

        $transactionCode = $this->generateTransactionCode();
        try {

            $dataDeposit = Deposit::findByUserId($userId);

            $saldoFirts =  $dataDeposit->first()->saldo;
            $totalBayar = $request->input('totalBayar');
            $finalSaldo = $saldoFirts - $totalBayar;

            $dataDepositForUpdate = array(            
                'saldo'  => $finalSaldo,
                'previous_saldo' => $dataDeposit->first()->saldo,         
                'updated_at' => $now,
                'updated_by' => $request->input('updatedBy'),
            );

            $dataDepositTransaction = array(
                'transaction_code' => $transactionCode,
                'debet' => $totalBayar,
                'kredit' => 0,
                'transaction_date' => $now,
                'created_by' => $request->input('updatedBy'),
                'type' => '0',
                'deposit_id' => $dataDeposit->first()->depositId

            );

            $pinSha = sha1($request->input('pin'));
            $dataUser = User::findByIdAndPin($userId, $pinSha);

            if(!empty($dataUser)){
                if(!empty($dataDeposit) && $saldoFirts >= $totalBayar)
                {


                    $debit = Deposit::debetOrKredit($dataDeposit->first()->depositId, $dataDepositForUpdate, $dataDepositTransaction);

                    $code = $debit ? 200 : 400;
                    $ress = Response::response($code);

                    return $ress;

                }else{
                    $ress = Response::response(400);
                    return $ress;
                }

            }else{
                 $ress = Response::responseWithMessage(400,"pin salah");
                    return $ress;
            }
            
            
        } catch (Exception $e) {
            return $e;
        }

    }

    public function kredit(Request $request, $userId){

        $code = 400;
        $ressMessage = "";

        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s');        

        $transactionCode = $this->generateTransactionCode();
        try {

            $dataDeposit = Deposit::findByUserId($userId);

            

            if(count($dataDeposit) == 0)
            {                
                $totalBayar = $request->input('totalBayar');

                $dataKredit = array(            
                    'saldo'  => $totalBayar,
                    'previous_saldo' => 0,         
                    'created_at' => $now,
                    'created_by' => $request->input('updatedBy'),
                    'user_id' => $userId,
                    'transaction_code' => $transactionCode,
                    'debet' => 0,
                    'kredit' => $totalBayar,
                    'transaction_date' => $now,                    
                    'type' => '3'
                );                
            
                $kredit = Deposit::firstKredit($dataKredit);
                

                $code = $kredit ? 200 : 400;
                $ressMessage = $code == 200 ? "kredit: ". $totalBayar .", saldo: ". $totalBayar : "failed";
                
                $ress = Response::responseWithMessage($code, $ressMessage);
                

                return $ress;
            }else{

                $saldoFirts =  $dataDeposit->first()->saldo;
                $totalBayar = $request->input('totalBayar');
                $finalSaldo = $saldoFirts + $totalBayar;

                $dataDepositForUpdate = array(            
                    'saldo'  => $finalSaldo,
                    'previous_saldo' => $dataDeposit->first()->saldo,         
                    'updated_at' => $now,
                    'updated_by' => $request->input('updatedBy'),
                );

                $dataDepositTransaction = array(
                    'transaction_code' => $transactionCode,
                    'debet' => 0,
                    'kredit' => $totalBayar,
                    'transaction_date' => $now,
                    'created_by' => $request->input('updatedBy'),
                    'type' => '3',
                    'deposit_id' => $dataDeposit->first()->depositId

                );
                
                $kredit = Deposit::debetOrKredit($dataDeposit->first()->depositId, $dataDepositForUpdate, $dataDepositTransaction);

                $code = $kredit ? 200 : 400;
                
                $ressMessage = $code == 200 ? "kredit: ". $totalBayar .", saldo: ". $finalSaldo : "failed";
                $ress = Response::responseWithMessage($code, $ressMessage);
                return $ress;
            }
            
        } catch (Exception $e) {
            return $e;
        }

    }

    public function buyItem(Request $request, $userId){

        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s'); 

        $dataTransaction = array(
            "transaction_code" => $this->generateTransactionCode(),
            "transaction_date" => $now,
            "qty" => $request->input('qty'),
            "user_id" => $userId,
            "total" => $request->input('total'),
            "items" => $request->input('items')


        );

        $result = Transactions::buyItem($dataTransaction);

        $dataTransactionItem = TransactionItem::findByTransactionId($result->id);

        $buildData = [];

        foreach ($dataTransactionItem as $value) {
            array_push($buildData, 
                [
                    "transaction_items_id" => $value->transaction_items_id,
                    "transactionId" => $value->transaction_id,
                    "price" => $value->price,
                    "name" => $value->name,
                    "qty" => $value->qty

                ]
            );
        }

        $buildDataResult = [];

        array_push($buildDataResult, 
                [
                    'transactionCode' => $result->transaction_code, 
                    'transactionDate' => $result->transaction_date,
                    'total' => (int) $result->total,
                    'qty' =>  (int) $result->qty,
                    'userId' => (int) $result->user_id,
                    'transactionId' => (int) $result->transactionId,
                    'items' => $buildData
                    
                ]
            );

        
        // return $result;
        return Response::response(200, $buildDataResult);
        
    }


    
}
