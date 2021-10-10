<?php

namespace App\Http\Controllers;
use App\Models\Transactions;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\DepositTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Response;

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

        $totalSantri = User::findAll();
        $totalDeposit = DepositTransaction::getAllKredit(1, $year, $month);
        $totalTransaksi = DepositTransaction::getAllDebit(1, $year, $month);

        $data = array(
            "totalSantriActive" => $totalSantri->total(),
            "totalDeposit" => $totalDeposit->total(),
            "totalTransaksi" => $totalTransaksi->total(),
            "bulan" => $month,
            "tahun" => $year
        );

        return Response::responseWithMessage(200, 'berhasil', $data);

    }

    
    
}
