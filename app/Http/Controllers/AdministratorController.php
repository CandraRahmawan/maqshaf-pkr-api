<?php

namespace App\Http\Controllers;
use App\Models\Administrator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Response;
use Illuminate\Support\Str;


class AdministratorController extends Controller
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
        $data = Administrator::getAll($limit);

        // $ress = Response::response(200, $data);

        foreach ($data as $value) {
            array_push($buildData, 
                [
                    'administratorId' => $value->administratorId, 
                    'fullName' => $value->fullName,
                    'username' => $value->username,
                    'token' => $value->token,                    
                    'createdAt' => $value->createdAt,
                    'createdBy' => $value->createdBy,
                    'updatedAt' => $value->updatedAt, 
                    'updatedBy' => $value->updatedBy

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
        $data = Administrator::findById($id);
        $ress = Response::response(200, $data);

        return $ress;
    }

    public function findByIdAndPassword($id, $pin){
        $pinSha1 = sha1($pin);        
        $data = Administrator::findByIdAndPin($id, $pinSha1);
        $ress = Response::response(200, $data);

        return $ress;
    }

    public function insert(Request $request){
        $dataAdmin = Administrator::findByToken($request->header('api_token'))->first()->username;

        $data = array(            
            'full_name'  => $request->input('fullName'),            
            'password' => sha1($request->input('password')),
            'username'  => $request->input('username'),
            'created_by' => $dataAdmin
        );

        $save = Administrator::insert($data);
        
        $ress = Response::response(200, $save);

        return $ress;

    }

    public function updateData(Request $request, $id){        
        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s');
        $dataAdmin = Administrator::findByToken($request->header('api_token'))->first()->username;

        $data = array(
            'full_name'  => $request->input('fullName'),            
            'username'  => $request->input('username'),         
            'updated_by' => $dataAdmin,
            'updated_at' => $now
        );

        $update = Administrator::updateData($id, $data);
        $code = $update ? 200 : 400;
        $ress = Response::response($code);

        return $ress;
    }

    public function updatePassword(Request $request, $id){
        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s');
        $dataAdmin = Administrator::findByToken($request->header('api_token'))->first()->username;
        
        $data = array(
            'username' => $request->input('username'),
            'password' => sha1($request->input('password')),          
            'updated_by' => $dataAdmin,
            'updated_at' => $now
        );
        $passOld = sha1($request->input('passwordOld'));
        $update = Administrator::updatePassword($id, $passOld, $data);

        $code = $update ? 200 : 400;
        $ress = Response::response($code);

        return $ress;
        
    }

    //belum memakai JWT
    public function login(Request $request){
        
            $username = $request->input('username');
            $password = sha1($request->input('password'));
        
        
        $login = Administrator::findByUsernameAndPassword($username, $password)->first();

        if(!empty($login) && $login->deletedAt == null){
            $token = Str::random(40);

            $data = array(
                'token' => $token
            );

            $update = Administrator::updateData($login->administratorId, $data);

            if($update){
                $dataLogin = Administrator::findById($login->administratorId)->first();
                $code = 200;
                $ress = Response::response($code, $dataLogin);
            }else{
                $code = 400;
                $message = "gagal ubah token";
                $ress = Response::responseWithMessage($code, $message);    
            }            

        }else{
            $code = 400;
            $message = "pasword salah";
            $ress = Response::responseWithMessage($code, $message);
        }
        
        

        return $ress;
    }

    public function deleteDataById(Request $request, $id) {

        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s');
        $dataAdmin = Administrator::findByToken($request->header('api_token'))->first()->username;

        $data = array(                   
            'delete_by' => $dataAdmin,
            'delete_at' => $now
        );

        $delete = Administrator::updateData($id, $data);
        $code = $delete ? 200 : 400;
        $ress = Response::response($code);

        return $ress;

    }

    public function logout(Request $request){
        $administratorId = $request->input('administratorId');

        $data = array(
                'token' => null
            );

        $update = Administrator::updateData($administratorId, $data);

        if($update){            
            $code = 200;
            $message = "logout success";
            $ress = Response::responseWithMessage($code, $message);

        }else{
            $code = 400;
            $message = "gagal logout";
            $ress = Response::responseWithMessage($code, $message);
        }

        return $ress;
    }

    public function resetPassword(Request $request, $id){
        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s');
        $dataAdmin = Administrator::findByToken($request->header('api_token'))->first()->username;

        $data = array(
            'password' => sha1('admin123'),           
            'updated_by' => $dataAdmin,
            'updated_at' => $now
        );
        
        $update = Administrator::updateData($id, $data);

        $code = $update ? 200 : 400;
        $ress = Response::response($code);

        return $ress;
        
    }
}
