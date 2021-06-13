<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class ExampleController extends Controller
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

    //

    public function getAll(){
        // foreach (User::all() as $user) {
        //     echo $user->full_name;
        // }

        // $results = User->findAll();

        // return User::findAll();

        return User::selectTest();
    }
}
