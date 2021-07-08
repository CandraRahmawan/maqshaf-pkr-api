<?php

namespace App\Http\Controllers;
use App\Models\MasterGoods;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Response;

class MasterGoodController extends Controller
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

        $data = MasterGoods::getAll();
        $ress = Response::response(200, $data);
        
        return $ress;
    }

    public function findById($id){        
        $data = MasterGoods::findById($id);
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

        $save = MasterGoods::insert($data);
        
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

        $update = MasterGoods::updateData($id, $data);
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

        $delete = MasterGoods::updateData($id, $data);
        $code = $delete ? 200 : 400;
        $ress = Response::response($code);

        return $ress;

    }

    public function uploadImage(Request $request, $id){
         
        if( $request->file('image_file') ) {

            $path = $request->file('image_file')->getRealPath();
            $logo = file_get_contents($path);
            $base64 = base64_encode($logo);    
            

            $data = array(
                'image' => $base64
            );            

            $update = MasterGoods::updateData($id, $data);

            return $update;
        } else {
            return "image error";
        }

    }
}
