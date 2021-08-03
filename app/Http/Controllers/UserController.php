<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Deposit;
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
    

    public function findAll(){        

        $data = User::findAll();
        $ress = Response::response(200, $data);
        
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
        $data = array(
            'nis' => $request->input('nis'),
            'full_name'  => $request->input('fullName'),
            'class'  => $request->input('class'),
            'address' => $request->input('address'),
            // 'pin' => sha1($request->input('pin')),
            'pin' => sha1(111111),
            'created_by' => $request->input('createdBy'),
        );

        $save = User::insert($data);
        return $save;

    }

    public function updateData(Request $request, $id){
        $data = array(
            'nis' => $request->input('nis'),
            'full_name'  => $request->input('fullName'),
            'class'  => $request->input('class'),
            'address' => $request->input('address'),            
            'updated_by' => $request->input('updatedBy'),
        );

        $update = User::updateData($id, $data);
        $ress = $update ? "success" : "failed";
        return $ress;
    }

    public function updatePin(Request $request, $id){
        $data = array(
            'pin' => sha1($request->input('pin')),          
            'updated_by' => $request->input('updatedBy'),
            'updated_at' => $now = date('Y-m-d H:i:s')
        );
        $pinOld = sha1($request->input('oldPin'));
        $update = User::updatePin($id, $pinOld, $data);
        
        return $update;
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
                return Response::response(200);
            }     
        
        }else if(!empty($userId)){
            $dataUser = User::findById($userId);

            if(!empty($dataUser->first())){

                return $this->buildJsonDataSaldo($dataUser->first(), $dataUser->first()->userId);

            }else{
                return Response::response(200);
            }  
        }else{
            $message = "nis and idUser cannot be null";
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

}
