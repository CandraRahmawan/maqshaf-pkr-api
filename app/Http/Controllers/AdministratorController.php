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
    

    public function findAll(){        

        $data = Administrator::getAll();
        $ress = Response::response(200, $data);
        
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
        $data = array(            
            'full_name'  => $request->input('fullName'),
            'password' => sha1('admin123'),
            'username'  => $request->input('username'),
            'created_by' => $request->input('createdBy')            
        );

        $save = Administrator::insert($data);
        
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

        $update = Administrator::updateData($id, $data);
        $code = $update ? 200 : 400;
        $ress = Response::response($code);

        return $ress;
    }

    public function updatePassword(Request $request, $id){
        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s');

        $data = array(
            'username' => $request->input('username'),
            'password' => sha1($request->input('password')),          
            'updated_by' => $request->input('updatedBy'),
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
                $message = "failed update token";
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

        $data = array(                   
            'delete_by' => $request->input('deleteBy'),
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
            $message = "failed logout";
            $ress = Response::responseWithMessage($code, $message);
        }

        return $ress;
    }
}
