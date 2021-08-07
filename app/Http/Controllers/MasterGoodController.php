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
        $limit = $request->input('limit');
        $data = MasterGoods::getAll();        

        $buildData = [];

        foreach ($data as $value) {
            array_push($buildData, 
                [
                    'masterGoodsId' => $value->masterGoodsId, 
                    'name' => $value->name,
                    'image' => env('APP_URL').'/mastergood/image/'.$value->masterGoodsId,
                    'description' =>  $value->description,
                    'price' => (int)$value->price,
                    'isActive' => $value->isActive,
                    'code' => $value->code,
                    'currency' => $value->currency, 
                    'category' => $value->category,
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
        
        // $ress = Response::response(200, $buildData);
        $ress = Response::responseWithPage(200, $buildData, $dataPagination[0]);
        return $ress;
    }

    public function findById($id){        
        $data = MasterGoods::findById($id)->first();

        $buildData = [];

        if(!empty($data)){

            array_push($buildData, 
                [
                    'masterGoodsId' => $data->masterGoodsId, 
                    'name' => $data->name,
                    'image' => env('APP_URL').'/mastergood/image/'.$data->masterGoodsId,
                    'description' =>  $data->description,
                    'price' => (int)$data->price,
                    'isActive' => $data->isActive,
                    'code' => $data->code,
                    'currency' => $data->currency, 
                    'category' => $data->category,
                    'createdAt' => $data->createdAt,
                    'createdBy' => $data->createdBy,
                    'updatedAt' => $data->updatedAt,
                    'updatedBy' => $data->updatedBy
                ]
            );
        }

        $ress = Response::response(200, $buildData);

        return $ress;
    }

    public function insert(Request $request){

        if( $request->file('image_file') ) {
            // Get the file from the request            

              $file = $request->file('image_file');

            // Get the contents of the file
            $contents = $file->openFile()->fread($file->getSize());

            $data = array(            
                'name'  => $request->input('name'),            
                'description'  => $request->input('description'),
                'price'  => $request->input('price'),
                'is_active'  => 1,
                'code'  => $request->input('code'),            
                'created_by' => $request->input('createdBy'),
                'image' => $contents
            );

            $save = MasterGoods::insert($data);

            $ress = $this->findById($save->id);

            // $ress = Response::response(200);


        }else {            
            $ress = Response::responseWithMessage(400, "image error");
        }
        

        return $ress;

    }

    public function updateData(Request $request, $id){        
        date_default_timezone_set('Asia/Jakarta'); # add your city to set local time zone
        $now = date('Y-m-d H:i:s');

        if($request->file('image_file')) {
            // Get the file from the request

            $file = $request->file('image_file');
            $contents = $file->openFile()->fread($file->getSize());

            $data = array(
                'name'  => $request->input('name'),            
                'description'  => $request->input('description'),
                'price'  => $request->input('price'),
                'is_active'  => $request->input('isActive'),
                'code'  => $request->input('code'),
                'updated_by' => $request->input('updated_by'),
                'updated_at' => $now,
                'image' => $contents,
                'category' => $request->input('category')
            );

            $update = MasterGoods::updateData($id, $data);
            $code = $update ? 200 : 400;
            $ress = Response::response($code);


        }else {            
            $ress = Response::responseWithMessage(400, "update data image error");
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

        $delete = MasterGoods::updateData($id, $data);
        $code = $delete ? 200 : 400;
        $ress = Response::response($code);

        return $ress;

    }

    public function uploadImage(Request $request, $id){

        if( $request->file('image_file') ) {
            // Get the file from the request

            $path = $request->file('image_file')->getRealPath();
            $logo = file_get_contents($path);
            $base64 = base64_encode($logo);

            $data = array(
                'image' => $base64
            );         

            $update = MasterGoods::updateData($id, $data);

            return Response::response(200);
        } else {
            return "image error";
        }

    }


    public function getImage($id){
        $data = MasterGoods::findById($id)->first();
        return $data->image;
    }

    public function findByName(Request $request){        
        $buildData = [];

        $nameInput = $request->input('name');


        if(strlen($nameInput) != 0){
            $data = MasterGoods::findByName($nameInput);

            foreach ($data as $value) {
                array_push($buildData, 
                    [
                        'masterGoodsId' => $value->masterGoodsId, 
                        'name' => $value->name,
                        'image' => env('APP_URL').'/mastergood/image/'.$value->masterGoodsId,
                        'description' =>  $value->description,
                        'price' => (int)$value->price,
                        'isActive' => $value->isActive,
                        'code' => $value->code,
                        'currency' => $value->currency, 
                        'category' => $value->category,
                        'createdAt' => $value->createdAt,
                        'createdBy' => $value->createdBy,
                        'updatedAt' => $value->updatedAt,
                        'updatedBy' => $value->updatedBy
                    ]
                );
            }
        }else{
            return $this->findAll();
        }


        $ress = Response::response(200, $buildData);

        return $ress;
    }
}
