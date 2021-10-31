<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\view;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class Invoice implements FromCollection, WithHeadings
{
    public function collection()
    {
        // return User::all();
        return view('invoices', ['datas' => User::all()]);
    }

    // public function collection()
    // {
        
    //     return User::all();
    // }

    // public function headings(): array
    //     {
    //         return [
    //             'Heading 1',
    //             'Heading 2',
    //             'Heading 3',  
    //         ];
    //     }
}
