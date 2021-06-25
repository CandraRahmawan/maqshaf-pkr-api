<?php

namespace App\Http\Controllers;
use App\Models\User;
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
}
