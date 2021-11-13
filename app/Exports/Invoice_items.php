<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Transactions;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Invoice_items implements FromCollection, WithHeadings
{
    protected $year;
    protected $month;

    function __construct($year, $month) {
        $this->year = $year;
        $this->month = $month;
    }

    public function headings(): array
    {
        return [            
            'Code',
            'Tanggal',
            'Nama Barang',
            'Harga',
            'Jumlah Barang Yang dibeli',
            'NIS',
            'Nama Lengkap',
            'Kelas'
        ];
    }

    public function collection()
    {
        return Transactions::printAllMastergodsSoldOut($this->year, $this->month);
    }
}

