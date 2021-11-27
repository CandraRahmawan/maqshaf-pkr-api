<?php

namespace App\Http\Controllers;
use App\Models\Transactions;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\DepositTransaction;
use App\Models\Deposit;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Response;
use DateTime;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\Invoice_debet;
use App\Exports\Invoice_kreadit;
use App\Exports\Invoice_items;

use Illuminate\Http\Response as ResponseObject;

class TransactionsController extends Controller
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

        $data = Transactions::getAll();
        // $data = TransactionItem::getAll();
        $ress = Response::response(200, $data);
        
        return $ress;
    }

    public function findById($id){        
        $data = Transactions::findById($id);
        $ress = Response::response(200, $data);

        return $ress;
    }    

    public function insert(Request $request){



        $data = array(            
            'name'  => $request->input('name'),            
            'description'  => $request->input('description'),
            'price'  => $request->input('price'),
            'is_active'  => 1,
            'code'  => $request->input('code'),            
            'created_by' => $request->input('createdBy')            
        );

        $save = Transactions::insert($data);
        
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

        $update = Transactions::updateData($id, $data);
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

        $delete = Transactions::updateData($id, $data);
        $code = $delete ? 200 : 400;
        $ress = Response::response($code);

        return $ress;

    }

    public function dashboard(Request $request){
        $year = $request->input('year');
        $month = $request->input('month');

        
        $totalDeposit = DepositTransaction::getAllKreditByYearAndMonth(1, $year, $month);
        $totalDepositAmount = DepositTransaction::getAllKreditAmountByYearAndMonth($year, $month);
        $totalTransaksi = DepositTransaction::getAllDebitByYearAndMonth(1, $year, $month);
        $totalTransaksiAmount = DepositTransaction::getAllDebitAmountByYearAndMonth($year, $month);

        // return $totalDeposit;
        $data = array(            
            "totalDeposit" => $totalDeposit->total(),
            "totalDepositAmount" => (int) $totalDepositAmount,
            "totalTransaksi" => $totalTransaksi->total(),
            "totalTransaksiAmount" => (int) $totalTransaksiAmount,
            "bulan" => $month,
            "tahun" => $year
        );

        return Response::responseWithMessage(200, 'berhasil', $data);


    }

    public function dashboardAll(){

        $totalSantri = User::findAll();
        $totalDeposit = DepositTransaction::getAllKredit();
        $totalDepositAmount = DepositTransaction::getAllKreditAmount();
        $totalTransaksi = DepositTransaction::getAllDebit();
        $totalTransaksiAmount = DepositTransaction::getAllDebitAmount();
        $totalWithDrawl = DepositTransaction::getAllWithDrawl();
        $totalWithDrawlAmount = DepositTransaction::getAllWithDrawlAmount();
        // return $totalWithDrawl;

        $totalAllDebet = $totalTransaksiAmount + $totalWithDrawlAmount;
        $sisaSaldoAll = $totalDepositAmount - $totalAllDebet;

        $data = array(
            "totalSantriActive" => $totalSantri->total(),
            "totalDepositAll" => $totalDeposit->total(),
            "totalDepositAmountAll" => (int) $totalDepositAmount,
            "totalTransaksiAll" => $totalTransaksi->total(),
            "totalTransaksiAmountAll" => (int) $totalTransaksiAmount,
            "totalWithDrawlAll" => $totalWithDrawl->total(),
            "totalWithDrawlAmount" => (int) $totalWithDrawlAmount,
            "sisaSaldoAll" => $sisaSaldoAll
            
        );

        return Response::responseWithMessage(200, 'berhasil', $data);        
    }

    
    public function print(Request $request){
        $year = $request->input('year');
        $month = $request->input('month');
        $uuid = $this->generateTransactionCode();
        $nameFile = "ExportData_Debet_".date_timestamp_get(new DateTime());

        

        
        

        return Excel::download(new Invoice_debet($year, $month), $nameFile.".xlsx");

        
        return $nameFile;

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

    public function kreditPrint(Request $request){
        $year = $request->input('year');
        $month = $request->input('month');
        $uuid = $this->generateTransactionCode();
        $nameFile = "ExportData_Kredit_".$uuid."_.xlsx";        

        // Excel::download(new Invoice_kreadit($year, $month), $nameFile.".xlsx");
        Excel::download(new Invoice_kreadit($year, $month), $nameFile);

        
        return DepositTransaction::printAllKreditFindByTrxCOde($year, $month);

    }

    public function mastergoodsItemSoldOutPrint(Request $request){
        $year = $request->input('year');
        $month = $request->input('month');
        $uuid = $this->generateTransactionCode();
        $nameFile = "ExportData_mastergoods_".$uuid."_.xlsx";        

        // Excel::download(new Invoice_kreadit($year, $month), $nameFile.".xlsx");
        Excel::download(new Invoice_items($year, $month), $nameFile);

        
        return Transactions::printAllMastergodsSoldOut($year, $month);

    }

    
}
