<?php

namespace App\Http\Controllers;
use App\Models\Deposit;
use App\Models\DepositTransaction;
use App\Models\TransactionItem;
use App\Models\Transactions;
use App\Models\User;
use App\Models\Administrator;
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

    public function debet(Request $request, $userId){

        $code = 400;

        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s');        

        $transactionCode = $this->generateTransactionCode();
        try {

            $dataDeposit = Deposit::findByUserId($userId);

            $pinSha = sha1($request->input('pin'));
            $dataUser = User::findByIdAndPin($userId, $pinSha);
            

            if(!empty($dataUser->first())){
                if(empty($dataUser->first()->isDelete)){

                    if(!empty($dataDeposit->first())){

                        $saldoFirts =  $dataDeposit->first()->saldo;
                        $totalBayar = $request->input('total');
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
                            'deposit_id' => $dataDeposit->first()->depositId,
                            'transaction_id' => $request->input('transactionId')

                        );

                        if($saldoFirts >= $totalBayar)
                        {
                        //insert and update data transaksi
                            $debit = Deposit::debetOrKredit($dataDeposit->first()->depositId, $dataDepositForUpdate, $dataDepositTransaction);

                            $code = $debit ? 200 : 400;

                        // Total bayar Rp 17.500, sisa saldo anda adalah Rp 15.0000


                            $ressMessage = $code == 200 ? "Total bayar Rp ". number_format($totalBayar,0,',','.') .", sisa saldo anda adalah Rp ". number_format($finalSaldo,0,',','.') : "bad request";

                            $ress = Response::responseWithMessage($code, $ressMessage);



                        }else{
                            $ress = Response::responseWithMessage(400,"saldo Tidak mencukupi");

                        }

                    }else{
                        $ress = Response::responseWithMessage(400,"data Deposit User Tidak ditemukan");                    

                    }
                }else{
                    $ress = Response::responseWithMessage(400,"data User Is Delete");                    

                }
            }else{
               $ress = Response::responseWithMessage(400,"pin salah");

           }


       } catch (Exception $e) {
        return $e;
    }

    return $ress;

}

public function kredit(Request $request, $userId){

    $code = 400;
    $ressMessage = "";
    $dataAdmin = Administrator::findByToken($request->header('api_token'))->first()->username;

        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s');        

        $transactionCode = $this->generateTransactionCode();
        try {

            $dataDeposit = Deposit::findByUserId($userId);            

            if(count($dataDeposit) == 0)
            {                
                $totalBayar = $request->input('saldo');

                $dataKredit = array(            
                    'saldo'  => $totalBayar,
                    'previous_saldo' => 0,         
                    'created_at' => $now,
                    'created_by' => $dataAdmin,
                    'user_id' => $userId,
                    'transaction_code' => $transactionCode,
                    'debet' => 0,
                    'kredit' => $totalBayar,
                    'transaction_date' => $now,                    
                    'type' => '3',
                    'transactionId' => null
                );                

                $kredit = Deposit::firstKredit($dataKredit);
                

                $code = $kredit ? 200 : 400;
                $ressMessage = $code == 200 ? "kredit: ". $totalBayar .", saldo: ". $totalBayar : "bad request";
                
                $ress = Response::responseWithMessage($code, $ressMessage);
                

                return $ress;
            }else{

                $saldoFirts =  $dataDeposit->first()->saldo;
                $totalBayar = $request->input('saldo');
                $finalSaldo = $saldoFirts + $totalBayar;

                $dataDepositForUpdate = array(            
                    'saldo'  => $finalSaldo,
                    'previous_saldo' => $dataDeposit->first()->saldo,         
                    'updated_at' => $now,
                    'updated_by' => $dataAdmin,
                );

                $dataDepositTransaction = array(
                    'transaction_code' => $transactionCode,
                    'debet' => 0,
                    'kredit' => $totalBayar,
                    'transaction_date' => $now,
                    'created_by' => $dataAdmin,
                    'type' => '3',
                    'deposit_id' => $dataDeposit->first()->depositId,
                    'transaction_id' => null

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

        $validateBuy = $this->cekSaldoUserMinPrice($request->input('total'), $userId); 

        // return $validateBuy;       

        if($validateBuy['result'])
        {
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
                        "transaction_items_id" => $value->transactionItemsId,
                        "transactionId" => $value->transactionId,
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
                    'saldo' => (int)$validateBuy['saldo'],
                    'transactionId' => (int) $result->id,
                    'items' => $buildData
                    
                ]
            );

            return Response::response(200, $buildDataResult[0]);
        }else{
            return Response::responseWithMessage(400, $validateBuy['message']);
        }        
        
    }

    public function cekSaldoUserMinPrice($totalBayar, $userId){


        $dataDeposit = Deposit::findByUserId($userId);
        

        if(!empty($dataDeposit->first())){

            $saldoFirts =  $dataDeposit->first()->saldo;            

            if($saldoFirts >= $totalBayar){

                $result = array(
                    "message" => "berhasil",
                    "result" => true,
                    "saldo" => (int)$saldoFirts,
                    "totalBayar" => (int)$totalBayar
                );

            }else{

                $result = array(
                    "message" => "saldo tidak mencukupi",
                    "result" => false,
                    "saldo" => (int)$saldoFirts,
                    "totalBayar" => (int)$totalBayar
                );

            }

        }else{
            $result = array(
                "message" => "data not found",
                "result" => false,
                "saldo" => (int)0,
                "totalBayar" => (int)$totalBayar
            );
        }

        return $result;
    }

    public function findByAllKredit(Request $request){
        $limit = $request->input('limit');
        $year = $request->input('year');
        $month = $request->input('month');

        $data = DepositTransaction::getAllKredit($limit, $year, $month);
        
        $buildData = $this->buildDataDebitOrKreditAll($data);

        $dataPagination = array([
            "total" => $data->total(),
            "data_in_this_page" => $data->count(),
            "data_per_page" => $data->perPage(),
            "current_page" => $data->currentPage(),
            "last_page" => $data->lastPage(),
            "next_page_url" => $data->nextPageUrl(),
            "prev_page_url" => $data->previousPageUrl()
        ]);
        
        // $ress = Response::response(200, $buildData);
        $ress = Response::responseWithPage(200, $buildData, $dataPagination[0]);
        return $ress;
    }

    public function findByAllDebet(Request $request){
        $limit = $request->input('limit');
        $year = $request->input('year');
        $month = $request->input('month');
        $data = DepositTransaction::getAllDebit($limit,  $year, $month);
        // return $data;
        
        $buildData = $this->buildDataDebitOrKreditAll($data, true);

        $dataPagination = array([
            "total" => $data->total(),
            "data_in_this_page" => $data->count(),
            "data_per_page" => $data->perPage(),
            "current_page" => $data->currentPage(),
            "last_page" => $data->lastPage(),
            "next_page_url" => $data->nextPageUrl(),
            "prev_page_url" => $data->previousPageUrl()
        ]);
        
        
        $ress = Response::responseWithPage(200, $buildData, $dataPagination[0]);
        return $ress;
    }

    public function buildDataDebitOrKreditAll($data, $debet = false){

        $buildData = [];

        foreach ($data as $value) {
            array_push($buildData, 
                [
                    'depositTransactionId' => $value->deposit_transaction_id, 
                    'transactionCode' => $value->transaction_code,
                    'debet' => (int)$value->debet,
                    'kredit' =>  (int)$value->kredit,
                    'transactionDate' => $value->transaction_date,
                    'createdBy' => $value->created_by,
                    'type' => $value->type,
                    'depositId' => $value->deposit_id, 
                    'transactionId' => $value->transaction_id,
                    'total' => $value->total ? $value->total : null,
                    "qty" => $value->qty ? $value->qty : null,
                    "userId" => $value->user_id ? $value->user_id: null,
                    "nis" => $value->nis ? $value->nis : null,
                    "fullName" => $value->full_name ? $value->full_name : null,
                    "class" => $value->class ? $value->class : null,
                    "address" => $value->address ? $value->address : null,
                    "listItem" => $debet ? TransactionItem::findByTransactionId($value->transaction_id) : []

                ]
            );
        }

        return $buildData;

    }

    public function withDrawl(Request $request, $id){

        $saldoPull = $request->input('saldo');

        $code = 400;

        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s');        

        $transactionCode = $this->generateTransactionCode();

        $dataUser = User::findById($id)->first();

        $dataDepositUser = Deposit::findByUserId($id)->first();

        $dataAdmin = Administrator::findByToken($request->header('api_token'))->first()->username;        
        

        if(!empty($dataUser) && !empty($dataDepositUser)){
            if(empty($dataUser->isDelete)){

                $totalSaldo = $dataDepositUser->saldo - $saldoPull;            

                if($totalSaldo >= 0){                

                    $dataDepositForUpdate = array(            
                        'saldo'  => $totalSaldo,
                        'previous_saldo' => $dataDepositUser->saldo,         
                        'updated_at' => $now,
                        'updated_by' => $dataAdmin,
                    );


                    $dataDepositTransaction = array(
                        "transaction_code" => $transactionCode,
                        "debet" => $saldoPull,
                        "kredit" => 0,
                        "transaction_date" => $now,
                        "type" => 2,
                        "deposit_id" => $dataDepositUser->depositId,
                        "created_by" => $dataAdmin,
                        "transaction_id" => null,
                    );

                    $dataWithDrawl = Deposit::debetOrKredit($dataDepositUser->depositId, $dataDepositForUpdate, $dataDepositTransaction);

                    $code = $dataWithDrawl ? 200 : 400;       

                    $message = $code == 200 ? "Total Pengambilan Rp ". $saldoPull .", sisa saldo anda adalah Rp ". $totalSaldo : "bad request";

                    $ress = Response::responseWithMessage($code, $message);

                // return $ress;

                }else{
                    $message = "saldo tidak mencukupi, total saldo anda = " . $dataDepositUser->saldo;
                    $ress = Response::responseWithMessage($code, $message);
                }
            }else{
                $message = "user Data Deleted";
                $ress = Response::responseWithMessage($code, $message);    
            }
        }else{
            $message = "user not found";
            $ress = Response::responseWithMessage($code, $message);
        }

        return $ress;
    }

    public function findAllKreditByTrxCode(Request $request){
        $limit = $request->input('limit');
        $trxCode = $request->input('transactionCode');
        $year = $request->input('year');
        $month = $request->input('month');

        $data = DepositTransaction::getAllKreditFindByTrxCOde($limit, $trxCode, $year, $month);


        $buildData = $this->buildDataDebitOrKreditAll($data);

        $dataPagination = array([
            "total" => $data->total(),
            "data_in_this_page" => $data->count(),
            "data_per_page" => $data->perPage(),
            "current_page" => $data->currentPage(),
            "last_page" => $data->lastPage(),
            "next_page_url" => $data->nextPageUrl(),
            "prev_page_url" => $data->previousPageUrl()
        ]);

        // $ress = Response::response(200, $buildData);
        $ress = Response::responseWithPage(200, $buildData, $dataPagination[0]);
        return $ress;
    }

    public function findAllDebetNisOrTransactionCode(Request $request){
        $limit = $request->input('limit');
        $trxCode = $request->input('transactionCode');
        $nis = $request->input('nis');
        $year = $request->input('year');
        $month = $request->input('month');
        $data = DepositTransaction::getDebitByNisOrTransactionCode($limit, $nis, $trxCode, $year, $month);
        // return $data;

        $buildData = $this->buildDataDebitOrKreditAll($data, true);

        $dataPagination = array([
            "total" => $data->total(),
            "data_in_this_page" => $data->count(),
            "data_per_page" => $data->perPage(),
            "current_page" => $data->currentPage(),
            "last_page" => $data->lastPage(),
            "next_page_url" => $data->nextPageUrl(),
            "prev_page_url" => $data->previousPageUrl()
        ]);


        $ress = Response::responseWithPage(200, $buildData, $dataPagination[0]);
        return $ress;
    }


}
