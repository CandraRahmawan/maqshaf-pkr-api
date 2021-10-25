<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Administrator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Response;

class UserController extends Controller
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
    

    public function findAll(Request $request){        
        $limit = $request->input('limit');

        $buildData = [];
        $data = User::findAllUserAndDeposit($limit);
        // return $data;

        foreach ($data as $value) {
            array_push($buildData, 
                [
                    'userId' => $value->userId, 
                    'nis' => $value->nis,
                    'fullName' => $value->fullName,
                    'class' =>  (int)$value->class,
                    'address' => $value->address,
                    'createdAt' => $value->createdAt,
                    'createdBy' => $value->createdBy,
                    'updatedAt' => $value->updatedAt, 
                    'updatedBy' => $value->updatedBy,
                    'isDelete' => $value->isDelete,
                    'deletedAt' => $value->deletedAt,
                    'deletedBy' => $value->deletedBy,
                    'saldo' => $value->saldo ? (int) $value->saldo : 0

                ]
            );
        }
        

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

    public function findById($id){        
        $data = User::findById($id);
        $ress = Response::response(200, $data);

        return $ress;
    }

    public function findByIdAndPin($id, $pin){
        $pinSha1 = sha1($pin);        
        $data = User::findByIdAndPin($id, $pinSha1);
        $ress = Response::response(200, $data);

        return $ress;
    }

    public function insert(Request $request){
        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s');
        
        $dataAdmin = Administrator::findByToken($request->header('api_token'))->first()->username;
        $data = array(
            'nis' => $request->input('nis'),
            'full_name'  => $request->input('fullName'),
            'class'  => $request->input('class'),
            'address' => $request->input('address'),            
            'pin' => sha1('111111'),
            'created_by' => $dataAdmin,
            'created_at' => $now
        );

        $dataUser = User::findByNis($request->input('nis'));
        
        
        if(!empty($dataUser->first())){
            $code = 400;
            $message = "nis sudah terpakai";
            $save = $dataUser->first();

        }else{  
            $save = User::insert($data);
            if($save){
                $code = 200;
                $message = "tambah data berhasil";

            }else{
                $code = 400;
                $message = "tambah data gagal";
            }            
        }

        $ress = Response::responseWithMessage($code, $message, $save);
        

        return $ress;

    }

    public function updateData(Request $request, $id){
        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $dataAdmin = Administrator::findByToken($request->header('api_token'))->first()->username;

        $data = array(
            'nis' => $request->input('nis'),
            'full_name'  => $request->input('fullName'),
            'class'  => $request->input('class'),
            'address' => $request->input('address'),            
            'updated_by' => $dataAdmin,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $dataUser = User::findByNis($request->input('nis'));
        $originalDataUser = User::findById($id);

        
        if(($originalDataUser->first() == $dataUser->first())){
            
            try {
                $update = User::updateData($id, $data);
                $code = 200;
                $message = "ubah data berhasil";
            } catch (Exception $e) {
                $code = 400;
                $message = "ubah data gagal";
                $update = null;
            }

        }else{
            $code = 400;
            $message = "nis sudah terpakai";
            $update = $dataUser->first();


        }
        return Response::responseWithMessage($code, $message);

        
    }

    public function updatePin(Request $request, $id){
        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone

        $data = array(
            'pin' => sha1($request->input('pin')),          
            'updated_by' => $request->input('updatedBy'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $pinOld = sha1($request->input('oldPin'));
        $update = User::updatePin($id, $pinOld, $data);        

        if($update){
            $code = 200;
            $message = "ubah pin berhasil";
        }else{
            $code = 400;
            $message = "ubah pin gagal";
        }

        return Response::responseWithMessage($code, $message);
        
    }

    public function userSaldo($id){

        $dataUser = User::findById($id);
        

        if(!empty($dataUser->first())){

            return $this->buildJsonDataSaldo($dataUser->first(), $id);

        }else{
            return Response::response(400);
        }        

    }

    public function userFindByNameAndClass(Request $request){
        $name = $request->input('name');
        $class = $request->input('class');

        $data = User::findByNameAndClass($name, $class);

        return Response::response(200, $data);

    }

    public function findByNis(Request $request){        
        $nis = $request->input("nis");
        $userId = $request->input("userId");        

        if(!empty($nis)){
            $dataUser = User::findByNis($nis);

            if(!empty($dataUser->first())){

                return $this->buildJsonDataSaldo($dataUser->first(), $dataUser->first()->userId);

            }else{
                $message = "Siswa Tidak ditemukan";
                return Response::responseWithMessage(400, $message);
            }     

        }else if(!empty($userId)){
            $dataUser = User::findById($userId);

            if(!empty($dataUser->first())){

                return $this->buildJsonDataSaldo($dataUser->first(), $dataUser->first()->userId);

            }else{
                $message = "Siswa Tidak ditemukan";
                return Response::responseWithMessage(400, $message);
            }
        }else{
            $message = "nis and idUser tidak boleh kosong";
            return Response::responseWithMessage(400, $message);
            
        }
        
    }

    public function buildJsonDataSaldo($dataUser, $userId){
        $buildData = [];

        $userDeposit = Deposit::findByUserId($userId);

        if(!empty($userDeposit)){

            array_push($buildData, 
                [                    
                    'user' => $dataUser,
                    'deposit' => $userDeposit->first()
                ]
            );

        }else{
            array_push($buildData, 
                [
                    'user' => $dataUser, 
                    'deposit' => null
                ]
            );
        }

        return Response::responseWithoutArray(200, $buildData);        
    }

    public function resetPin(Request $request, $id){
        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $data = array(
            'pin' => sha1('111111'),          
            'updated_by' => $request->input('updatedBy'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        $update = User::updateData($id, $data);

        if($update){
            $code = 200;
            $message = "reset pin berhasil";
        }else{
            $code = 400;
            $message = "reset pin gagal";
        }

        return Response::responseWithMessage($code, $message);
        
    }

    public function deletById(Request $request, $id){
        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $dataAdmin = Administrator::findByToken($request->header('api_token'))->first()->username;

        $cekSaldoUser = Deposit::findByUserId($id)->first();
        

        if(empty($cekSaldoUser) || $cekSaldoUser->saldo < 0){
            $data = array(
                'is_delete' => 'YES',
                'deleted_by' => $dataAdmin,
                'deleted_at' => date('Y-m-d H:i:s')
            );

            try {
                $update = User::updateData($id, $data);

                if($update){
                    $code = 200;
                    $message = "hapus user berhasil";
                }else{
                    $code = 400;
                    $message = "hapus user gagal";
                }

            } catch (Exception $e) {
                return $e;

            }

        }else{
            return Response::responseWithMessage(400, "user mempunyai saldo", $cekSaldoUser);
        }
        
        return Response::responseWithMessage($code, $message);
    }

    public function activedById(Request $request, $id){
        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $dataAdmin = Administrator::findByToken($request->header('api_token'))->first()->username;

        $data = array(
            'is_delete' => null,
            'updated_by' => $dataAdmin,
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        try {
            $update = User::updateData($id, $data);

            if($update){
                $code = 200;
                $message = "active user berhasil";
            }else{
                $code = 400;
                $message = "active user gagal";
            }

        } catch (Exception $e) {
            return $e;

        }
        return Response::responseWithMessage($code, $message);
    }

    public function findNameOrNis(Request $request){
        $name = $request->input('name');
        $nis = $request->input('nis');
        $limit = $request->input('limit');        
        $buildData = [];

        $data = User::findByNameAndNis($limit, $name, $nis);       

        foreach ($data as $value) {
            array_push($buildData, 
                [
                    'userId' => $value->userId, 
                    'nis' => $value->nis,
                    'fullName' => $value->fullName,
                    'class' =>  (int)$value->class,
                    'address' => $value->address,
                    'createdAt' => $value->createdAt,
                    'createdBy' => $value->createdBy,
                    'updatedAt' => $value->updatedAt, 
                    'updatedBy' => $value->updatedBy,
                    'isDelete' => $value->isDelete,
                    'deletedAt' => $value->deletedAt,
                    'deletedBy' => $value->deletedBy,
                    'saldo' => $value->saldo ? (int) $value->saldo : 0

                ]
            );
        }


        $dataPagination = Response::buildPagination($data);

        $ress = Response::responseWithPage(200, $buildData, $dataPagination[0]);

        return $ress;
    }
    

}
